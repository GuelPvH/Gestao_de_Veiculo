# syntax=docker/dockerfile:1
# =============================================================================
# Node — Vite dev server e build de assets.
# Existe para o Node nunca precisar ser instalado no host.
# =============================================================================

ARG NODE_VERSION=24

FROM node:${NODE_VERSION}-alpine

# Default 82/82 = www-data do Alpine, igual ao container PHP. Ver compose.yaml.
ARG UID=82
ARG GID=82

# su-exec: drop de privilégio no entrypoint (o build não roda como root).
RUN apk add --no-cache su-exec

WORKDIR /var/www/html

# Alinhamento de uid/gid com o host (relevante em Linux nativo). O usuário
# `node` da imagem oficial já é 1000:1000, então em Docker Desktop é no-op.
RUN set -eux; \
    if [ "$(id -u node)" != "$UID" ] || [ "$(id -g node)" != "$GID" ]; then \
        deluser node; \
        if ! getent group "$GID" > /dev/null; then addgroup -g "$GID" node; fi; \
        group_name="$(getent group "$GID" | cut -d: -f1)"; \
        adduser -u "$UID" -D -H -G "$group_name" -h /home/node -s /bin/sh node; \
    fi; \
    mkdir -p /home/node; \
    chown "$(id -u node):$(id -g node)" /home/node; \
    id node

COPY docker/node/entrypoint.sh /usr/local/bin/entrypoint
RUN set -eux; \
    sed -i 's/\r$//' /usr/local/bin/entrypoint; \
    chmod +x /usr/local/bin/entrypoint

ENV npm_config_cache=/home/node/.npm

ENTRYPOINT ["entrypoint"]
CMD ["npm", "run", "dev"]
