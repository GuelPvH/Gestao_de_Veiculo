#!/bin/bash
# =============================================================================
# Cria o database de TESTE ao lado do de desenvolvimento.
#
# Teste nunca deve rodar contra o banco de desenvolvimento. A suíte usa SQLite
# em memória por padrão (phpunit.xml), mas se o projeto passar a depender de
# recurso específico do MySQL (coluna JSON, fulltext, ENUM), o SQLite mente —
# e aí basta apontar .env.testing para este database.
#
# É um .sh e não um .sql porque só assim dá para interpolar MYSQL_DATABASE /
# MYSQL_USER, que vêm do compose. Roda uma única vez, no primeiro boot do
# volume de dados.
# =============================================================================
set -euo pipefail

: "${MYSQL_DATABASE:?MYSQL_DATABASE não definido}"
: "${MYSQL_USER:?MYSQL_USER não definido}"
: "${MYSQL_ROOT_PASSWORD:?MYSQL_ROOT_PASSWORD não definido}"

TEST_DATABASE="${MYSQL_DATABASE}_testing"

echo "[init] criando database de teste: ${TEST_DATABASE}"

mysql --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS \`${TEST_DATABASE}\`
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON \`${TEST_DATABASE}\`.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
EOSQL

echo "[init] database de teste pronto"
