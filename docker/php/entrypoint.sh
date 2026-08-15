#!/bin/sh
# =============================================================================
# Entrypoint do container PHP.
#
# Roda como root apenas o suficiente para garantir que os diretórios graváveis
# existam e pertençam ao usuário da aplicação; em seguida derruba o privilégio
# e executa o comando como www-data. A aplicação nunca fica rodando como root.
#
# Idempotente. Nunca usa `chmod 777` — em storage/ isso é falha de segurança,
# não solução.
# =============================================================================
set -eu

APP_USER="${APP_USER:-www-data}"
APP_GROUP="${APP_GROUP:-www-data}"
APP_ROOT="${APP_ROOT:-/var/www/html}"

log() { printf '[entrypoint] %s\n' "$*" >&2; }

is_root() { [ "$(id -u)" -eq 0 ]; }

# Diretórios que o Laravel precisa gravar em runtime.
WRITABLE_DIRS='
storage
storage/app
storage/app/public
storage/app/private
storage/debugbar
storage/framework
storage/framework/cache
storage/framework/cache/data
storage/framework/sessions
storage/framework/testing
storage/framework/views
storage/logs
bootstrap/cache
'

for dir in $WRITABLE_DIRS; do
    mkdir -p "${APP_ROOT}/${dir}"
done
mkdir -p "${APP_ROOT}/vendor"

if is_root; then
    # Em bind mount do Docker Desktop o driver ignora chown/chmod. Isso não é
    # erro: a gravabilidade real é verificada logo abaixo e falha alto se for
    # o caso. Em Linux nativo o chown é o que de fato resolve a permissão.
    # `bootstrap` entra na lista, e não apenas `bootstrap/cache`: quando o
    # mkdir acima cria o diretório pai como root, o usuário da aplicação fica
    # sem poder gravar dentro dele — e a instalação do Laravel falha ao
    # escrever bootstrap/app.php com um "Permission denied" sem contexto.
    if ! chown -R "${APP_USER}:${APP_GROUP}" \
            "${APP_ROOT}/storage" "${APP_ROOT}/bootstrap" 2>/dev/null; then
        log "chown em storage/ e bootstrap/ não teve efeito (esperado em bind mount do Docker Desktop)."
    fi

    # `vendor` é volume nomeado: no primeiro boot vem vazio e pertencente ao
    # root. Basta ajustar o ponto de montagem — recursivo aqui custaria dezenas
    # de milhares de inodes a cada start.
    chown "${APP_USER}:${APP_GROUP}" "${APP_ROOT}/vendor"

    if ! chmod -R u+rwX,g+rwX "${APP_ROOT}/storage" "${APP_ROOT}/bootstrap" 2>/dev/null; then
        log "chmod em storage/ e bootstrap/ não teve efeito (esperado em bind mount do Docker Desktop)."
    fi
fi

# Verificação real de escrita. Se o usuário da aplicação não consegue gravar,
# o container morre agora com uma mensagem clara — em vez de servir um 500
# incompreensível na primeira request.
for dir in storage/logs storage/framework/views bootstrap bootstrap/cache vendor; do
    probe="${APP_ROOT}/${dir}/.entrypoint-write-probe"
    if is_root; then
        if ! su-exec "${APP_USER}" sh -c "touch '${probe}' && rm -f '${probe}'"; then
            log "ERRO: ${APP_USER} não consegue gravar em ${dir}."
            log "      Corrija a permissão no host ou ajuste APP_UID/APP_GID no .env."
            exit 1
        fi
    else
        if ! (touch "${probe}" && rm -f "${probe}"); then
            log "ERRO: sem permissão de escrita em ${dir} (rodando como uid $(id -u))."
            exit 1
        fi
    fi
done

if is_root; then
    # Exceção única ao drop de privilégio: o master do php-fpm precisa iniciar
    # como root para abrir os logs do container e fazer setuid dos workers
    # para www-data (docker/php/fpm/www.conf). O código da aplicação roda nos
    # workers, como www-data. Qualquer outro comando — artisan, composer,
    # queue:work, schedule:work, pest — é rebaixado aqui.
    case "$1" in
        php-fpm)
            exec "$@"
            ;;
    esac

    exec su-exec "${APP_USER}" "$@"
fi

exec "$@"
