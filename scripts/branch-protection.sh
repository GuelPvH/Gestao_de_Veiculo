#!/bin/sh
# =============================================================================
# Aplica a branch protection do repositório no GitHub.
#
# POR QUE ISTO É UM SCRIPT, E NÃO UM PASSO NA INTERFACE WEB
#
# Os git hooks deste projeto bloqueiam commit e push na branch protegida, mas
# hook roda na máquina de quem commita: é configurado por clone
# (`make hook-install`) e é ignorável com `--no-verify`. Isso não é falha do
# CaptainHook — é como o Git funciona. Hook local é atalho para feedback rápido;
# o portão de verdade é do lado do servidor.
#
# Configuração que existe só na interface web é configuração que ninguém sabe
# que existe, ninguém revisa e ninguém consegue recriar. Aqui ela é código:
# versionada, revisável no PR e reaplicável.
#
# Ver docs/adr/0005-hooks-locais-e-branch-protection.md.
#
# -----------------------------------------------------------------------------
# REQUISITOS
#   - GitHub CLI (`gh`) autenticado: `gh auth login`
#   - permissão de ADMINISTRADOR no repositório
#   - o repositório precisa ter rodado o CI ao menos uma vez, para que os
#     checks abaixo já existam com estes nomes
#
# Este é o único script do projeto que roda no HOST: ele fala com a API do
# GitHub, não com a aplicação. Não há PHP, Composer ou Node envolvidos.
#
# USO
#   sh scripts/branch-protection.sh                  # branch master, sem review obrigatória
#   BRANCH=main sh scripts/branch-protection.sh      # outra branch
#   APROVACOES=1 sh scripts/branch-protection.sh     # exige 1 aprovação (ver aviso)
#
# CONFERIR O QUE ESTÁ APLICADO
#   gh api repos/{owner}/{repo}/branches/master/protection
# =============================================================================
set -eu

BRANCH="${BRANCH:-master}"

# -----------------------------------------------------------------------------
# ATENÇÃO ao valor padrão 0.
#
# O GitHub não permite que uma pessoa aprove o próprio PR. Em repositório de uma
# pessoa só, exigir 1 aprovação com `enforce_admins` ligado trava TODO merge —
# inclusive o do dono. Por isso o padrão é 0: a proteção continua valendo (PR
# obrigatório, CI verde obrigatório), só não exige um segundo par de olhos que
# ainda não existe.
#
# No dia em que o time tiver mais de uma pessoa, rode com APROVACOES=1. É a
# mudança que faz o code review virar regra em vez de hábito.
# -----------------------------------------------------------------------------
APROVACOES="${APROVACOES:-0}"

if ! command -v gh >/dev/null 2>&1; then
    echo "FALHA: o GitHub CLI (gh) nao esta instalado."
    echo "       https://cli.github.com  — depois: gh auth login"
    exit 1
fi

if ! gh auth status >/dev/null 2>&1; then
    echo "FALHA: o gh nao esta autenticado. Rode: gh auth login"
    exit 1
fi

REPO="$(gh repo view --json nameWithOwner -q .nameWithOwner)"

echo "Repositorio: $REPO"
echo "Branch:      $BRANCH"
echo "Aprovacoes:  $APROVACOES"
echo

# -----------------------------------------------------------------------------
# Os nomes em `contexts` são os nomes dos JOBS no CI (o campo `name:` de cada
# job em .github/workflows/). Se um job for renomeado e esta lista não
# acompanhar, o check obrigatório passa a apontar para algo que não existe: a
# proteção fica frouxa SEM AVISO NENHUM.
#
# Renomeou job? Atualize aqui e rode o script de novo.
# -----------------------------------------------------------------------------
cat <<JSON > /tmp/branch-protection.json
{
  "required_status_checks": {
    "strict": true,
    "contexts": [
      "Qualidade (lint, análise, testes)",
      "Smoke test (stack completa)",
      "Build da imagem de produção",
      "Gitleaks e política de arquivos",
      "Conventional Commits"
    ]
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "required_approving_review_count": ${APROVACOES},
    "dismiss_stale_reviews": true,
    "require_code_owner_reviews": true
  },
  "restrictions": null,
  "required_linear_history": true,
  "required_conversation_resolution": true,
  "allow_force_pushes": false,
  "allow_deletions": false,
  "block_creations": false,
  "lock_branch": false,
  "allow_fork_syncing": false
}
JSON

# Notas sobre as opções acima, na ordem em que aparecem:
#
#   strict: true
#       A branch precisa estar atualizada com a base antes do merge. Sem isso,
#       dois PRs verdes isoladamente podem quebrar a master ao se encontrarem.
#
#   enforce_admins: true
#       A regra vale para o dono do repositório também. Regra que o admin pode
#       ignorar é a regra que será ignorada exatamente no dia corrido.
#
#   dismiss_stale_reviews: true
#       Push novo invalida aprovação anterior — aprovou-se outro código.
#
#   require_code_owner_reviews: true
#       Caminhos listados em .github/CODEOWNERS exigem review de quem responde
#       por eles (docker/, config/, migrations, Policies, .github/).
#
#   required_linear_history: true
#       Combina com merge por squash: a história da master fica legível, e é o
#       que dá sentido ao check do titulo do PR.
#
#   required_conversation_resolution: true
#       Comentário de review em aberto bloqueia o merge. Impede o clássico
#       "mergeei antes de ver".
#
#   allow_force_pushes / allow_deletions: false
#       Ninguém reescreve nem apaga a branch principal.

echo "Aplicando..."
if gh api -X PUT "repos/$REPO/branches/$BRANCH/protection" \
        -H "Accept: application/vnd.github+json" \
        --input /tmp/branch-protection.json > /tmp/branch-protection-result.json; then
    rm -f /tmp/branch-protection.json
    echo
    echo "  OK  protecao aplicada em $REPO@$BRANCH"
    echo
    echo "Confira com:"
    echo "  gh api repos/$REPO/branches/$BRANCH/protection"
else
    echo
    echo "FALHA ao aplicar a protecao. Causas mais comuns:"
    echo "  - voce nao e administrador do repositorio"
    echo "  - a branch '$BRANCH' nao existe no remoto"
    echo "  - repositorio privado em conta gratuita (branch protection exige plano pago)"
    echo "  - um dos checks obrigatorios nunca rodou, entao o nome ainda nao existe"
    echo
    echo "Resposta da API:"
    cat /tmp/branch-protection-result.json
    exit 1
fi
