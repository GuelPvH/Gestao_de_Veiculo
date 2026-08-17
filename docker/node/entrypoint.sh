#!/bin/sh
# =============================================================================
# Entrypoint do container Node.
#
# Garante que os diretórios escritos pelo build (node_modules, public/build)
# existam e pertençam ao usuário `node`, e então executa o comando sem root.
# =============================================================================
set -eu

NODE_USER="${NODE_USER:-node}"
APP_ROOT="${APP_ROOT:-/var/www/html}"

log() { printf '[entrypoint:node] %s\n' "$*" >&2; }

is_root() { [ "$(id -u)" -eq 0 ]; }

# Ajusta o dono de um diretório de artefatos.
#
# O caso normal é barato: só o ponto de montagem. O caso caro — chown -R — só
# acontece quando o diretório foi populado por OUTRO uid, o que ocorre ao mudar
# APP_UID ou ao reaproveitar um volume/pasta de uma configuração anterior. Sem
# isso o npm falha com um EACCES que não diz de onde veio.
ensure_owned() {
    dir="$1"
    want_uid="$2"
    owner_spec="$3"

    mkdir -p "${dir}"

    # A verificação olha o CONTEÚDO, não só o diretório: corrigir apenas o topo
    # deixa `assets/` e `manifest.json` com o uid antigo, e o build morre com um
    # EACCES apontando para um caminho que aparenta estar correto.
    # `stat` em vez de `find -uid`: o find do busybox não traz esse predicado e
    # falha em silêncio, fazendo a verificação passar sempre.
    stray=''
    for entry in "${dir}"/* "${dir}"/.[!.]*; do
        [ -e "${entry}" ] || continue
        if [ "$(stat -c '%u' "${entry}" 2>/dev/null)" != "${want_uid}" ]; then
            stray="${entry}"
            break
        fi
    done

    dir_uid="$(stat -c '%u' "${dir}" 2>/dev/null || echo '')"

    if [ -n "${stray}" ] || [ "${dir_uid}" != "${want_uid}" ]; then
        log "${dir} contém arquivos de outro uid (esperado ${want_uid}). Ajustando (uma vez)..."
        if ! chown -R "${owner_spec}" "${dir}" 2>/dev/null; then
            log "chown em ${dir} não teve efeito (esperado em bind mount do Docker Desktop)."
        fi
    fi
}

if is_root; then
    node_uid="$(id -u "${NODE_USER}")"
    owner_spec="${node_uid}:$(id -g "${NODE_USER}")"

    ensure_owned "${APP_ROOT}/node_modules" "${node_uid}" "${owner_spec}"
    ensure_owned "${APP_ROOT}/public/build" "${node_uid}" "${owner_spec}"
    ensure_owned "/home/${NODE_USER}/.npm" "${node_uid}" "${owner_spec}"

    # Verificação real de escrita: falha alto e cedo em vez de deixar o build
    # morrer com um EACCES no meio da transformação de assets.
    for dir in node_modules public/build; do
        probe="${APP_ROOT}/${dir}/.entrypoint-write-probe"
        if ! su-exec "${NODE_USER}" sh -c "touch '${probe}' && rm -f '${probe}'"; then
            log "ERRO: ${NODE_USER} (uid ${node_uid}) não consegue gravar em ${dir}."
            exit 1
        fi
    done

    exec su-exec "${NODE_USER}" "$@"
fi

exec "$@"
