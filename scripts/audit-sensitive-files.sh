#!/usr/bin/env bash

set -Eeuo pipefail

readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly RESET='\033[0m'

failures=0
forbidden_files=()

is_forbidden_file() {
    local path="$1"
    local name="${path##*/}"

    case "$path" in
        .env.example)
            return 1
            ;;
        .env | .env.* | */.env | */.env.*)
            return 0
            ;;
    esac

    case "$name" in
        auth.json | .pypirc | .netrc | id_rsa | id_ed25519)
            return 0
            ;;
        *.pem | *.key | *.p12 | *.pfx | *.jks | *.keystore)
            return 0
            ;;
        *.sql | *.sql.gz | *.dump | *.bak | *.backup | *.sqlite | *.sqlite3 | *.db)
            return 0
            ;;
    esac

    case "$path" in
        .secrets/* | */.secrets/*)
            return 0
            ;;
    esac

    return 1
}

while IFS= read -r path; do
    if is_forbidden_file "$path"; then
        forbidden_files+=("$path")
    fi
done < <(git ls-files)

if ((${#forbidden_files[@]} > 0)); then
    printf '%bFALHA: arquivos sensíveis estão versionados:%b\n' "$RED" "$RESET" >&2
    printf '  - %s\n' "${forbidden_files[@]}" >&2
    failures=1
fi

if [[ -f .env.example ]]; then
    mapfile -t unsafe_env_lines < <(
        awk '
            /^[[:space:]]*($|#)/ { next }
            {
                separator = index($0, "=")
                if (separator == 0) { next }

                key = substr($0, 1, separator - 1)
                value = substr($0, separator + 1)
                gsub(/^[[:space:]]+|[[:space:]]+$/, "", key)
                gsub(/^[[:space:]]+|[[:space:]]+$/, "", value)
                gsub(/^['"'"']|['"'"']$/, "", value)

                normalized_key = toupper(key)
                if (normalized_key !~ /(PASSWORD|PASSWD|SECRET|TOKEN|PRIVATE_KEY|APP_KEY|DSN|ACCESS_KEY)/) {
                    next
                }

                if (value == "" || value == "null" || value ~ /^\$\{/) {
                    next
                }

                printf "%d:%s", NR, key
            }
        ' .env.example
    )

    if ((${#unsafe_env_lines[@]} > 0)); then
        printf '%bFALHA: .env.example contém valor em campo sensível:%b\n' "$RED" "$RESET" >&2
        printf '  - linha/chave %s\n' "${unsafe_env_lines[@]}" >&2
        printf 'Use valor vazio; o valor real deve vir do ambiente ou do gerenciador de segredos.\n' >&2
        failures=1
    fi
fi

if [[ -f .npmrc ]]; then
    mapfile -t unsafe_npm_lines < <(
        awk '
            /^[[:space:]]*($|#|;)/ { next }
            {
                separator = index($0, "=")
                if (separator == 0) { next }

                key = substr($0, 1, separator - 1)
                gsub(/^[[:space:]]+|[[:space:]]+$/, "", key)

                if (tolower(key) ~ /(_authtoken|_auth|password|username|token)/) {
                    printf "%d:%s", NR, key
                }
            }
        ' .npmrc
    )

    if ((${#unsafe_npm_lines[@]} > 0)); then
        printf '%bFALHA: .npmrc contém configuração de autenticação:%b\n' "$RED" "$RESET" >&2
        printf '  - linha/chave %s\n' "${unsafe_npm_lines[@]}" >&2
        printf 'Mantenha tokens e credenciais somente no ambiente local ou no secret store.\n' >&2
        failures=1
    fi
fi

unpinned_actions=()
while IFS= read -r workflow; do
    while IFS= read -r reference; do
        [[ -z "$reference" ]] && continue
        [[ "$reference" == ./* ]] && continue

        if [[ "$reference" == docker://* ]]; then
            if [[ ! "$reference" =~ ^docker://.+@sha256:[0-9a-f]{64}$ ]]; then
                unpinned_actions+=("$workflow: $reference")
            fi
        elif [[ ! "$reference" =~ ^[^@]+@[0-9a-f]{40}$ ]]; then
            unpinned_actions+=("$workflow: $reference")
        fi
    done < <(
        sed -nE "s/^[[:space:]]*(-[[:space:]]*)?uses:[[:space:]]*['\"]?([^'\"#[:space:]]+).*/\\2/p" "$workflow"
    )
done < <(find .github/workflows -type f \( -name '*.yml' -o -name '*.yaml' \) -print | sort)

if ((${#unpinned_actions[@]} > 0)); then
    printf '%bFALHA: Actions externas devem usar SHA completo e imutável:%b\n' "$RED" "$RESET" >&2
    printf '  - %s\n' "${unpinned_actions[@]}" >&2
    failures=1
fi

if ((failures > 0)); then
    exit 1
fi

printf '%bOK: política de arquivos sensíveis e Actions imutáveis atendida.%b\n' "$GREEN" "$RESET"
