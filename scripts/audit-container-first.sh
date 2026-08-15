#!/bin/sh
# =============================================================================
# Auditoria Container First.
#
# Documentação que ensina comando de host é pior que documentação nenhuma: o
# próximo desenvolvedor segue, instala PHP na máquina dele e o ambiente diverge.
# Este script falha se README, Makefile, make.ps1, docs/ ou scripts/ contiverem
# a INVOCAÇÃO de um binário do projeto fora de um container.
#
# Uma única exclusão é aplicada sobre o grep original: linhas cujo conteúdo
# começa com `identificador:`. São declarações de alvo do Make — `artisan:`,
# `composer:`, `npm:`, `rector:` — cujos nomes são exigidos pelo próprio plano.
# Uma invocação de comando nunca tem essa forma, então a exclusão não abre
# espaço para um comando de host passar despercebido.
#
# Uso:  sh scripts/audit-container-first.sh
# =============================================================================
set -u

TARGETS='README.md Makefile make.ps1 docs scripts'
BINARIES='php|composer|artisan|node|npm|npx|mysql|mysqldump|pest|phpstan|pint|rector'

# shellcheck disable=SC2086
FINDINGS="$(
    grep -rnE "^[[:space:]]*(\\\$ )?(${BINARIES})\\b" $TARGETS 2>/dev/null \
        | grep -v 'docker compose' \
        | grep -v 'docker run' \
        | grep -vE '^[^:]+:[0-9]+:[A-Za-z_][A-Za-z0-9_-]*:' \
        || true
)"

if [ -n "${FINDINGS}" ]; then
    echo "FALHA: comando de host encontrado na documentacao/scripts."
    echo "Cada linha abaixo precisa passar por 'docker compose' ou 'docker run':"
    echo
    echo "${FINDINGS}"
    exit 1
fi

echo "OK: nenhuma invocacao de binario do projeto fora de container."
exit 0
