#!/usr/bin/env bash
set -euo pipefail

# Note: Remember to give this file executable permissions (`chmod +x docker/captainhook/githook.sh`)!
# On Windows/WSL, you might need to run `git update-index --chmod=+x docker/captainhook/githook.sh` to track the permission in git.

# Git Bash (MSYS) reescreve caminhos estilo Unix em argumentos de linha de
# comando para caminhos Windows antes de repassá-los ao Docker — isso
# corrompe "/var/www/html" e faz o `docker compose exec -w` falhar com
# "Cwd must be an absolute path". MSYS_NO_PATHCONV desliga essa conversão.
export MSYS_NO_PATHCONV=1

# Único arquivo que roda fora do Docker. Bash puro, sem PHP — é o que
# mantém Container First até no gate de commit.
#
# CaptainHook já inclui "vendor/bin/captainhook" como primeiro argumento
# em "$@" (é ele quem monta a linha de comando no hook instalado em
# .git/hooks/) — não reintroduza o binário aqui, ou a chamada duplica e
# vira "Command "vendor/bin/captainhook" is not defined.".
docker compose up --no-recreate -d app >/dev/null 2>&1
exec docker compose exec -T -w /var/www/html app "$@"
