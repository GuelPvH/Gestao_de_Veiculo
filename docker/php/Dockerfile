# syntax=docker/dockerfile:1
# =============================================================================
# Imagem PHP-FPM. Multi-stage: `base` -> `dev` | `prod`.
# Nenhuma toolchain de linguagem extra (Rust/Python) é instalada aqui: sem caso
# de uso nominado, ela só inflaria a imagem e a superfície de ataque.
# =============================================================================

ARG PHP_VERSION=8.4

# Composer vem da imagem oficial, não de script baixado em runtime.
FROM composer:2 AS composer_bin


# -----------------------------------------------------------------------------
# base — extensões, usuário e configuração comuns a dev e prod
# -----------------------------------------------------------------------------
FROM php:${PHP_VERSION}-fpm-alpine AS base

ARG UID=82
ARG GID=82

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer \
    COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www/html

# Pacotes de runtime:
#   fcgi     -> cgi-fcgi, usado pelo healthcheck do FPM (§6.4)
#   su-exec  -> drop de privilégio no entrypoint, sem deixar root rodando a app
#   icu-libs -> runtime do intl;  libzip -> runtime do zip
#   git/unzip-> exigidos pelo Composer para instalar de dist/source
RUN apk add --no-cache \
        fcgi \
        git \
        icu-libs \
        libzip \
        su-exec \
        tzdata \
        unzip

# Extensões. Já vêm compiladas na imagem oficial e por isso NÃO são instaladas
# de novo: ctype, curl, fileinfo, mbstring, openssl, tokenizer, xml.
#   pdo_mysql -> banco            bcmath -> aritmética de precisão
#   intl      -> i18n/formatação  zip    -> Composer e pacotes
#   opcache   -> performance      pcntl  -> exigido por queue:work/schedule:work
#   redis     -> cache, sessão e fila (PECL)
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev linux-headers; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j"$(nproc)" bcmath intl opcache pcntl pdo_mysql zip; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    pecl clear-cache; \
    apk del .build-deps; \
    rm -rf /tmp/pear; \
    php -m

# Alinhamento de uid/gid com o host. Em Docker Desktop os defaults (82/82, que
# é o www-data do Alpine) já estão corretos e este bloco vira no-op.
RUN set -eux; \
    current_uid="$(id -u www-data)"; \
    current_gid="$(id -g www-data)"; \
    if [ "$current_uid" != "$UID" ] || [ "$current_gid" != "$GID" ]; then \
        deluser www-data; \
        if ! getent group "$GID" > /dev/null; then addgroup -g "$GID" www-data; fi; \
        group_name="$(getent group "$GID" | cut -d: -f1)"; \
        adduser -u "$UID" -D -H -G "$group_name" -h /var/www -s /sbin/nologin www-data; \
    fi; \
    id www-data

COPY --from=composer_bin /usr/bin/composer /usr/local/bin/composer

COPY docker/php/fpm/www.conf        /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/conf.d/app.ini      /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/entrypoint.sh       /usr/local/bin/entrypoint
COPY docker/php/healthcheck.sh      /usr/local/bin/php-fpm-healthcheck

# Normaliza CRLF: o repositório é editado em Windows e um `\r` no shebang faz o
# container morrer com "no such file or directory", erro que não diz nada.
RUN set -eux; \
    sed -i 's/\r$//' /usr/local/bin/entrypoint /usr/local/bin/php-fpm-healthcheck; \
    chmod +x /usr/local/bin/entrypoint /usr/local/bin/php-fpm-healthcheck

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm", "--nodaemonize"]


# -----------------------------------------------------------------------------
# dev — OPcache revalidando, Xdebug e PCOV disponíveis
# -----------------------------------------------------------------------------
FROM base AS dev

# PCOV para cobertura (rápido), Xdebug para step debugging (lento).
# Xdebug fica carregado porém em `mode=off`; liga-se pela env XDEBUG_MODE.
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers; \
    pecl install pcov xdebug; \
    docker-php-ext-enable pcov xdebug; \
    pecl clear-cache; \
    apk del .build-deps; \
    rm -rf /tmp/pear

COPY docker/php/conf.d/opcache-dev.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/conf.d/xdebug.ini      /usr/local/etc/php/conf.d/zzz-xdebug.ini

HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=5 \
    CMD ["php-fpm-healthcheck"]


# -----------------------------------------------------------------------------
# prod — código embutido, OPcache travado, sem dependências de dev
# -----------------------------------------------------------------------------
FROM base AS prod

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist \
        --no-autoloader --no-scripts

COPY --chown=www-data:www-data . .

RUN set -eux; \
    composer dump-autoload --no-dev --optimize --classmap-authoritative; \
    composer check-platform-reqs --no-dev; \
    mkdir -p storage/framework/cache/data storage/framework/sessions \
             storage/framework/views storage/logs bootstrap/cache; \
    chown -R www-data:www-data storage bootstrap/cache

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

# A configuração de OPcache de produção entra por ÚLTIMO: ela aponta
# `opcache.preload` para um arquivo dentro de storage/, que só existe depois do
# COPY do código acima.
COPY docker/php/conf.d/opcache-prod.ini /usr/local/etc/php/conf.d/zz-opcache.ini

HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=5 \
    CMD ["php-fpm-healthcheck"]
