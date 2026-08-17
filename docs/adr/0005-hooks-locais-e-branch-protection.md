# 0005 — Hooks locais são atalho; branch protection é o portão

- **Estado:** Aceita
- **Data:** 2026-08-17
- **Decide:** Miguel

## Contexto

O projeto usa CaptainHook para rodar verificação antes do commit e do push:
bloqueio de commit em branch protegida, Pint, PHPStan, detecção de segredo,
Conventional Commits, suíte completa no pre-push.

Isso resolve o problema de *feedback rápido* — o erro aparece em segundos, na
máquina de quem escreveu, e não vinte minutos depois no CI.

O que isso **não** resolve: hook de Git roda na máquina de quem commita, é
configurado por clone (`make hook-install`) e é ignorável com `--no-verify`.
Nenhuma dessas três propriedades é contornável, porque é assim que o Git
funciona. Um hook local, por definição, não é um portão: é uma conveniência de
quem coopera.

Havia aqui um risco concreto: o `CONTRIBUTING.md` afirmava que commit direto na
master é "bloqueado", o que criava a impressão de garantia onde havia só
convenção assistida.

## Decisão

Os dois mecanismos existem e têm papéis diferentes e declarados:

**Hooks locais (`captainhook.json`) — atalho, não portão.**
Existem para dar feedback em segundos. É esperado que sejam ignoráveis. Se um
hook está errado, corrige-se a regra em `captainhook.json`; não se contorna com
`--no-verify`.

**Branch protection no GitHub — o portão de verdade.**
É a única barreira que não depende da máquina de ninguém:

- `master` (e `main`) exigem Pull Request; push direto é recusado pelo servidor;
- os checks `Qualidade (lint, análise, testes)`, `Smoke test (stack completa)`,
  `Build da imagem de produção` e `Conventional Commits` são obrigatórios;
- a branch precisa estar atualizada com a base antes do merge;
- review de Code Owner é exigida nos caminhos do `.github/CODEOWNERS`;
- a regra vale **também para administradores** — regra que o dono do repositório
  pode ignorar é a regra que será ignorada exatamente no dia corrido.

A configuração está em `scripts/branch-protection.sh`, versionada. Ela é
aplicada uma vez, com permissão de administrador, e o script serve como registro
do que foi configurado — configuração que só existe na interface web do GitHub é
configuração que ninguém sabe que existe.

## Alternativas consideradas

- **Só hooks locais.** Depende de todo mundo rodar `make hook-install` e nunca
  usar `--no-verify`. Funciona até o primeiro dia de pressa.
- **Só branch protection.** Feedback vem do CI, minutos depois, e o
  erro de formatação de uma linha custa um ciclo inteiro de pipeline.
- **Hook no lado do servidor (`pre-receive`).** Só existe em GitHub Enterprise.

## Consequências

**Ganhos:** feedback rápido no local e garantia real no servidor; a garantia não
depende de disciplina individual; a configuração do repositório fica versionada e
revisável.

**Custos:**

- Duas configurações para manter em sintonia. Quando um check muda de nome no
  `ci.yml`, o `scripts/branch-protection.sh` precisa acompanhar — senão o check
  obrigatório aponta para um nome que não existe mais e a proteção fica frouxa
  sem avisar.
- Aplicar a proteção exige permissão de administrador, então não é feito por
  quem abre o PR.
- Toda mudança passa a exigir PR, incluindo correção de typo do próprio dono.

**Como sabemos que deu errado:** commit aparecendo direto na master, ou um check
obrigatório que nunca fica verde porque o nome mudou. Vale conferir a proteção
com `gh api repos/:owner/:repo/branches/master/protection` sempre que o `ci.yml`
mudar de nome de job.
