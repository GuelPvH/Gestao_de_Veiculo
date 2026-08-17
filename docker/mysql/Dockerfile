# syntax=docker/dockerfile:1
# =============================================================================
# MySQL 9.7 LTS.
#
# A tag e fixada na LTS de propósito. O repositório `mysql` publica duas trilhas
# nas mesmas tags: as LTS (8.4, 9.7 — esta é a apontada por `lts`) e as
# "innovation" (26.x, apontada por `latest`), que trocam de versão a cada
# trimestre e podem quebrar compatibilidade. Não troque para uma tag maior sem
# antes conferir se ela é LTS: `26.7` é mais nova que `9.7` no número, mas está
# na trilha errada.
#
# Configuração e scripts de init são EMBUTIDOS na imagem, não bind-montados:
# em Docker Desktop os arquivos do bind mount aparecem world-writable e o MySQL
# ignora silenciosamente qualquer arquivo de configuração nessa condição
# ("World-writable config file ... is ignored").
# =============================================================================

FROM mysql:9.7

COPY docker/mysql/conf.d/my.cnf              /etc/mysql/conf.d/zz-app.cnf
COPY docker/mysql/init/01-testing-db.sh      /docker-entrypoint-initdb.d/01-testing-db.sh

RUN set -eux; \
    sed -i 's/\r$//' /docker-entrypoint-initdb.d/01-testing-db.sh; \
    chmod 0644 /etc/mysql/conf.d/zz-app.cnf; \
    chmod 0755 /docker-entrypoint-initdb.d/01-testing-db.sh
