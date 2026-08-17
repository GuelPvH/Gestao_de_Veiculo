# syntax=docker/dockerfile:1
# =============================================================================
# MySQL 8.4 LTS.
#
# Configuração e scripts de init são EMBUTIDOS na imagem, não bind-montados:
# em Docker Desktop os arquivos do bind mount aparecem world-writable e o MySQL
# ignora silenciosamente qualquer arquivo de configuração nessa condição
# ("World-writable config file ... is ignored").
# =============================================================================

FROM mysql:26.7

COPY docker/mysql/conf.d/my.cnf              /etc/mysql/conf.d/zz-app.cnf
COPY docker/mysql/init/01-testing-db.sh      /docker-entrypoint-initdb.d/01-testing-db.sh

RUN set -eux; \
    sed -i 's/\r$//' /docker-entrypoint-initdb.d/01-testing-db.sh; \
    chmod 0644 /etc/mysql/conf.d/zz-app.cnf; \
    chmod 0755 /docker-entrypoint-initdb.d/01-testing-db.sh
