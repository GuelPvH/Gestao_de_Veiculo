# =============================================================================
# Atalhos do ambiente. Este arquivo encapsula Docker e NADA MAIS.
#
# Nenhum alvo invoca php, composer, node, npm ou mysql no host — se algum
# passar a invocar, o alvo está errado; corrija o alvo, não instale o binário.
#
# Windows sem `make`: use o runner equivalente `.\make.ps1 <alvo>`.
# =============================================================================

DC        := docker compose
RUN_APP   := $(DC) run --rm app
RUN_NODE  := $(DC) run --rm vite
# `exec` não passa pelo entrypoint, que é quem derruba o privilégio para
# www-data. Por isso o -u explícito: sem ele, arquivos nasceriam do root.
EXEC_APP  := $(DC) exec -u www-data app

.DEFAULT_GOAL := help
.PHONY: help setup up down restart build rebuild ps logs logs-app shell shell-root \
        install migrate seed key composer artisan npm test test-coverage lint \
        lint-fix analyse rector rector-apply insights check nginx-test db-shell \
        tinker queue-restart fresh destroy db-dump audit hook-install

help: ## Lista os alvos disponíveis
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

# -----------------------------------------------------------------------------
# Ciclo de vida
# -----------------------------------------------------------------------------
setup: ## Instalação completa do zero (idempotente)
	@test -f .env || cp .env.example .env
	$(DC) build
	$(RUN_APP) composer install --no-interaction --prefer-dist
	$(RUN_NODE) npm ci
	$(DC) up -d
	$(EXEC_APP) sh -c 'grep -q "^APP_KEY=base64:" .env || php artisan key:generate --force'
	$(EXEC_APP) php artisan migrate --force
	$(EXEC_APP) php artisan storage:link --force
	$(RUN_NODE) npm run build
	@echo ""
	@echo "Pronto. Aplicacao em http://localhost:$${APP_PORT:-8000}"

up: ## Sobe os containers
	$(DC) up -d

down: ## Para os containers (volumes e dados PRESERVADOS)
	$(DC) down

restart: ## Reinicia os containers
	$(DC) restart

build: ## Constrói as imagens
	$(DC) build

rebuild: ## Reconstrói as imagens ignorando cache
	$(DC) build --no-cache
	$(DC) up -d --force-recreate

ps: ## Estado dos containers
	$(DC) ps

logs: ## Segue o log de todos os serviços
	$(DC) logs -f --tail=100

logs-app: ## Segue o log de app, nginx e queue
	$(DC) logs -f --tail=100 app nginx queue

nginx-test: ## Valida a configuração do nginx
	$(DC) exec nginx nginx -t

# -----------------------------------------------------------------------------
# Shell e execução
# -----------------------------------------------------------------------------
shell: ## Shell no container app como www-data
	$(EXEC_APP) sh

shell-root: ## Shell no container app como root
	$(DC) exec app sh

db-shell: ## Cliente MySQL dentro do container do banco
	$(DC) exec mysql sh -c 'mysql -u"$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE"'

tinker: ## REPL do Laravel
	$(EXEC_APP) php artisan tinker

artisan: ## Comando artisan:  make artisan c="migrate:status"
	$(EXEC_APP) php artisan $(c)

composer: ## Comando composer: make composer c="require vendor/pacote"
	$(RUN_APP) composer $(c)

npm: ## Comando npm:      make npm c="install alguma-lib"
	$(RUN_NODE) npm $(c)

install: ## Instala dependências PHP e JS
	$(RUN_APP) composer install --no-interaction --prefer-dist
	$(RUN_NODE) npm ci

migrate: ## Aplica as migrations
	$(EXEC_APP) php artisan migrate

seed: ## Roda os seeders
	$(EXEC_APP) php artisan db:seed

key: ## Gera a APP_KEY
	$(EXEC_APP) php artisan key:generate

queue-restart: ## Faz o worker (Horizon) recarregar o código
	$(EXEC_APP) php artisan horizon:terminate

# -----------------------------------------------------------------------------
# Qualidade — a ordem de `check` não é arbitrária (§7.11):
# formata, revisa refactors, analisa o código já formatado, testa.
# -----------------------------------------------------------------------------
test: ## Suíte de testes
	$(EXEC_APP) ./vendor/bin/pest

test-coverage: ## Testes com cobertura mínima de 80%
	$(EXEC_APP) ./vendor/bin/pest --coverage --min=80

lint: ## Pint em modo verificação (não altera arquivo) — modo do CI
	$(EXEC_APP) ./vendor/bin/pint --test

lint-fix: ## Pint corrigindo só o que mudou no git
	$(EXEC_APP) ./vendor/bin/pint --dirty

analyse: ## Análise estática (Larastan/PHPStan)
	$(EXEC_APP) ./vendor/bin/phpstan analyse --memory-limit=1G

rector: ## Refactors sugeridos, sem aplicar
	$(EXEC_APP) ./vendor/bin/rector process --dry-run

rector-apply: ## Aplica os refactors — leia o diff do `make rector` antes
	$(EXEC_APP) ./vendor/bin/rector process

insights: ## PHP Insights (opcional)
	$(EXEC_APP) ./vendor/bin/phpinsights --no-interaction

check: lint rector analyse test ## Pipeline completo de qualidade

audit: ## Auditoria Container First — nenhum comando de host na documentacao
	$(RUN_APP) sh scripts/audit-container-first.sh

hook-install: ## Ativa os git hooks (CaptainHook) — roda uma vez por clone
	$(RUN_APP) vendor/bin/captainhook install --force

# -----------------------------------------------------------------------------
# Destrutivos — exigem confirmação escrita
# -----------------------------------------------------------------------------
fresh: ## APAGA E RECRIA AS TABELAS. Uso: make fresh CONFIRM=yes
	@if [ "$(CONFIRM)" != "yes" ]; then \
		echo "RECUSADO: 'make fresh' descarta TODOS os dados do banco."; \
		echo "Se e isso mesmo que voce quer: make fresh CONFIRM=yes"; \
		exit 1; \
	fi
	$(EXEC_APP) php artisan migrate:fresh --seed

destroy: ## APAGA OS VOLUMES (banco inclusive). Uso: make destroy CONFIRM=yes
	@if [ "$(CONFIRM)" != "yes" ]; then \
		echo "RECUSADO: 'make destroy' remove os volumes — o banco some junto."; \
		echo "Faca um dump antes:  make db-dump"; \
		echo "Se e isso mesmo que voce quer: make destroy CONFIRM=yes"; \
		exit 1; \
	fi
	$(DC) down -v

db-dump: ## Dump do banco para dump-AAAAMMDD-HHMM.sql
	$(DC) exec -T mysql sh -c 'mysqldump -u"$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE"' \
		> dump-$$(date +%Y%m%d-%H%M).sql
	@echo "Dump gravado."
