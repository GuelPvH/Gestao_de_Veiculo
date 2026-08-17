# syntax=docker/dockerfile:1
# =============================================================================
# Nginx — serve os estáticos de public/ e encaminha .php ao PHP-FPM.
# A configuração é embutida na imagem em vez de bind-montada: em Docker Desktop
# os arquivos do bind mount aparecem world-writable, e vários daemons ignoram
# arquivo de configuração nessa condição.
# =============================================================================

ARG NGINX_VERSION=1.29

FROM nginx:${NGINX_VERSION}-alpine

# curl: usado apenas pelo healthcheck.
RUN apk add --no-cache curl

COPY docker/nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf

# `nginx -t` NÃO roda aqui de propósito: o `fastcgi_pass app:9000` é resolvido
# por DNS na carga da configuração, e o host `app` só existe na rede do compose.
# A validação acontece no start do container (`make nginx-test`).

HEALTHCHECK --interval=15s --timeout=3s --start-period=10s --retries=5 \
    CMD curl -fsS http://127.0.0.1/nginx-health || exit 1
