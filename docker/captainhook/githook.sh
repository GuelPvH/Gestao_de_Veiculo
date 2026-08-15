#!/usr/bin/env bash
set -euo pipefail

# Note: Remember to give this file executable permissions (`chmod +x docker/captainhook/githook.sh`)!
# On Windows/WSL, you might need to run `git update-index --chmod=+x docker/captainhook/githook.sh` to track the permission in git.

# Único arquivo que roda fora do Docker. Bash puro, sem PHP — é o que
# mantém Container First até no gate de commit.
docker compose up --no-recreate -d app >/dev/null 2>&1
exec docker compose exec -T -w /var/www/html app vendor/bin/captainhook "$@"
