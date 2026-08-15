#!/bin/sh
# =============================================================================
# Healthcheck do PHP-FPM.
#
# PHP-FPM não tem endpoint HTTP: `curl localhost` no container app sempre falha.
# Aqui a consulta é feita falando FastCGI direto no socket TCP, contra o
# `ping.path` configurado em docker/php/fpm/www.conf.
#
# Sem `set -e` de propósito: a falha do cgi-fcgi é o resultado que este script
# precisa inspecionar e reportar, não um motivo para abortar sem mensagem.
# =============================================================================
set -u

FPM_HOST="${FPM_HOST:-127.0.0.1}"
FPM_PORT="${FPM_PORT:-9000}"
PING_PATH="${PING_PATH:-/fpm-ping}"

response="$(
    SCRIPT_NAME="${PING_PATH}" \
    SCRIPT_FILENAME="${PING_PATH}" \
    REQUEST_METHOD=GET \
    REQUEST_URI="${PING_PATH}" \
    QUERY_STRING='' \
    cgi-fcgi -bind -connect "${FPM_HOST}:${FPM_PORT}" 2>&1
)"

case "${response}" in
    *pong*) exit 0 ;;
esac

printf 'php-fpm healthcheck falhou em %s:%s%s\n%s\n' \
    "${FPM_HOST}" "${FPM_PORT}" "${PING_PATH}" "${response}" >&2
exit 1
