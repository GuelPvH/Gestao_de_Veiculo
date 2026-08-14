# syntax=docker/dockerfile:1.7

ARG PHP_VERSION=8.5.8
ARG COMPOSER_VERSION=2.10.2
ARG NODE_VERSION=24.18.0
ARG NGINX_VERSION=1.30.4-alpine3.24

FROM composer:${COMPOSER_VERSION} AS composer-bin

FROM node:${NODE_VERSION}-bookworm-slim AS node-base

FROM node-base AS frontend-build
WORKDIR /app

COPY package*.json ./
RUN if [ -f package-lock.json ]; then \
        npm ci --no-audit --no-fund; \
    else \
        npm install --no-audit --no-fund; \
    fi

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

FROM php:${PHP_VERSION}-fpm-bookworm AS php-base

ENV COMPOSER_HOME=/tmp/composer \
    PATH="/var/www/html/vendor/bin:${PATH}"

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        git \
        gosu \
        libcurl4 \
        libfreetype6 \
        libicu72 \
        libjpeg62-turbo \
        libonig5 \
        libpng16-16 \
        libpq5 \
        libwebp7 \
        libzip4 \
        unzip \
        ${PHPIZE_DEPS} \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libwebp-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        pcntl \
        pdo_pgsql \
        zip \
    && pecl install redis-6.3.0 \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove \
        ${PHPIZE_DEPS} \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libwebp-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

COPY --from=composer-bin /usr/bin/composer /usr/local/bin/composer
COPY docker/php/conf.d/app.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/fpm.conf /usr/local/etc/php-fpm.d/zz-log.conf
COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod 0755 /usr/local/bin/app-entrypoint \
    && mkdir -p \
        /var/www/html/bootstrap/cache \
        /var/www/html/storage/app/private \
        /var/www/html/storage/app/public \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm", "-F"]

FROM php-base AS development

ENV APP_ENV=local \
    APP_DEBUG=true

RUN cp "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"

COPY --from=node-base /usr/local/bin/node /usr/local/bin/node
COPY --from=node-base /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY --chown=www-data:www-data . .
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
        --no-interaction \
        --no-progress \
        --prefer-dist

COPY --from=frontend-build --chown=www-data:www-data /app/node_modules ./node_modules
COPY --from=frontend-build --chown=www-data:www-data /app/public/build ./public/build

FROM nginx:${NGINX_VERSION} AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=frontend-build /app/public /var/www/html/public

EXPOSE 80
HEALTHCHECK --interval=10s --timeout=3s --start-period=10s --retries=5 \
    CMD wget -q -O /dev/null http://127.0.0.1/up || exit 1

FROM php-base AS production

ENV APP_ENV=production \
    APP_DEBUG=false

RUN cp "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"
COPY docker/php/conf.d/opcache-production.ini /usr/local/etc/php/conf.d/98-opcache-production.ini

COPY --chown=www-data:www-data . .
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --classmap-authoritative \
    && rm -rf /tmp/composer \
    && mkdir -p \
        bootstrap/cache \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data bootstrap/cache storage

COPY --from=frontend-build --chown=www-data:www-data /app/public/build ./public/build

EXPOSE 9000
HEALTHCHECK --interval=10s --timeout=3s --start-period=15s --retries=5 \
    CMD ["php", "-r", "exit((int) ! @fsockopen('127.0.0.1', 9000));"]
