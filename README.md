# Gestão de Veículo — ambiente Laravel 100% Docker

> **PHP, Composer, Node, npm, MySQL e Redis não precisam estar instalados na sua máquina.**
> Só Docker, Docker Compose, Git e um editor.

Todo comando do projeto roda dentro de um container. Se algum passo deste
documento pedir para instalar uma dessas ferramentas no host, o passo está
errado — abra uma issue.

---

## 📚 Documentação

| Documento | Para quem |
|---|---|
| **[docs/INSTALACAO.md](docs/INSTALACAO.md)** | **Começa aqui.** Passo a passo do zero: WSL2, Docker Desktop, baixar e rodar o projeto, entrar no container. |
| **[docs/ARQUITETURA.md](docs/ARQUITETURA.md)** | Escopo completo: cada serviço, cada pacote instalado e o porquê de cada decisão. |
| **[CONTRIBUTING.md](CONTRIBUTING.md)** | Vai desenvolver? Fluxo de branch, commits, hooks, padrões de código e testes. |
| **[docs/adr/](docs/adr/README.md)** | *Por quê* de cada decisão que é cara de reverter — e como registrar a próxima. Leia antes de propor mudar um padrão. |
| **[SECURITY.md](SECURITY.md)** | Como reportar vulnerabilidade e o que é aceitável só em desenvolvimento. |
| Este README | Referência rápida do dia a dia de quem já está com o projeto no ar. |

### O caminho mais curto

```bash
cp .env.example .env     # preencha as senhas (DB_PASSWORD, MYSQL_ROOT_PASSWORD)
make setup               # ou .\make.ps1 setup no PowerShell
```

Depois abra <http://localhost:8000>. Para entrar no container:

```bash
docker exec -it veiculo_app bash
```

> `veiculo` é só o prefixo sugerido no `.env.example`. Você escolhe o seu em
> `CONTAINER_PREFIX` — vira `<prefixo>_app`, `<prefixo>_mysql`, etc.

---

## 1. Requisitos

| Ferramenta | Versão usada na validação | Como obter |
|---|---|---|
| Docker Engine + Compose v2 | 29.6.2 / v5.3.1 | [Docker Desktop](https://docs.docker.com/get-docker/) (Windows/macOS) ou o repositório oficial da Docker Inc. (Linux) |
| Git | qualquer versão recente | https://git-scm.com |
| Editor | à sua escolha | — |

No Linux, **não** instale o Docker pelo pacote `docker.io` da distro nem por
snap: o snap quebra bind mount, que é como o código chega nos containers.

---

## 2. Primeira instalação

```bash
git clone <url-do-repositorio>
cd Gestao_de_Veiculo_Programacao_Para_Web
```

**Linux / macOS / WSL:**

```bash
make setup
```

**Windows (PowerShell):**

```powershell
.\make.ps1 setup
```

> O Windows não traz `make`. `make.ps1` expõe exatamente os mesmos alvos e,
> como o `Makefile`, só invoca `docker compose` — nada é instalado no host.

O `setup` é idempotente: pode ser rodado de novo a qualquer momento. Ele faz,
nesta ordem (a ordem importa — cada passo depende do anterior):

```bash
cp .env.example .env                                # se ainda não existir
docker compose build
docker compose run  --rm app  composer install      # vendor precisa existir antes de subir
docker compose run  --rm vite npm ci                # run --rm, não exec: o serviço vite
                                                    # só sobe depois que node_modules existe
docker compose up -d                                # aguarda mysql ficar healthy
docker compose exec -u www-data app php artisan key:generate
docker compose exec -u www-data app php artisan migrate --force
docker compose run  --rm vite npm run build
```

Para popular a frota com dados de exemplo:

```bash
make seed          # Windows: .\make.ps1 seed
```

---

## 3. URLs

| Serviço | URL | Observação |
|---|---|---|
| Aplicação | http://localhost:8000 | porta em `APP_PORT` |
| Vite (dev server / HMR) | http://localhost:5175 | porta em `VITE_PORT` |
| phpMyAdmin | http://localhost:8082 | profile `tools`, publicado só em `127.0.0.1` |
| Mailpit | http://localhost:8026 | profile `tools`, captura todo e-mail |
| Pulse | http://localhost:8000/pulse | dashboard de observabilidade — requer `is_admin` |
| Horizon | http://localhost:8000/horizon | dashboard de filas Redis — requer `is_admin` |
| Health check raso | http://localhost:8000/up | só confirma que o PHP responde |
| Health check profundo | http://localhost:8000/up/deep | verifica app + DB + Redis — 503 se algo falhar |

Conflito de porta se resolve **no `.env`**, nunca no `compose.yaml`.

phpMyAdmin e Mailpit sobem junto porque o `.env` traz `COMPOSE_PROFILES=tools`.
Deixe essa variável vazia para não subi-los.

---

## 4. Arquitetura

```
rede: gestao-veiculo-ppw_net (bridge)

  navegador  ──:8000──▶  nginx  ──fastcgi :9000──▶  app (php-fpm)
      │                    ▲                            │
      │  :5175 HMR         │  mesmo bind mount          ├──▶ mysql  :3306  [volume]
      ▼                    │  do código                 └──▶ redis  :6379  [volume]
     vite ──────────────────┘                                  ▲
                                                     horizon ──┤  substitui queue:work
  profile "tools":  phpmyadmin :8082   mailpit :8026  scheduler ┘ schedule:work
```

O bind mount do código está no `nginx` **e** no `app`: o nginx resolve o
arquivo estático e monta o `SCRIPT_FILENAME` que envia ao FPM. Se o código
existisse só no `app`, toda request viraria `404 File not found`.

`vendor/` e `node_modules/` são **volumes nomeados**, não bind mount: em Docker
Desktop, sincronizar dezenas de milhares de arquivos pequenos com o host é
lento demais. Eles não precisam existir na sua máquina.

---

## 5. Comandos do dia a dia

Os dez mais usados (`make <alvo>` no Linux/macOS, `.\make.ps1 <alvo>` no Windows):

| Alvo | O que faz |
|---|---|
| `up` / `down` | sobe / para os containers (dados preservados) |
| `ps` | estado dos containers |
| `logs-app` | segue o log de app, nginx e queue |
| `shell` | shell no container da aplicação, como `www-data` |
| `artisan c="..."` | qualquer comando artisan |
| `composer c="..."` | qualquer comando composer |
| `npm c="..."` | qualquer comando npm |
| `test` | suíte Pest |
| `check` | pint → rector → phpstan → pest |
| `migrate` / `seed` | migrations e seeders |
| `hook-install` | ativa git hooks (CaptainHook) — uma vez por clone |

Exemplos:

```bash
make artisan  c="migrate:status"
make composer c="require spatie/laravel-permission"
make npm      c="install chart.js"
```

Sem `make`, os mesmos comandos por extenso:

```bash
docker compose exec -u www-data app php artisan migrate:status
docker compose run  --rm app  composer require spatie/laravel-permission
docker compose run  --rm vite npm install chart.js
docker compose exec -u www-data app ./vendor/bin/pest
docker compose exec -u www-data app ./vendor/bin/pint --dirty
docker compose exec -u www-data app ./vendor/bin/phpstan analyse --memory-limit=1G
```

> **Por que `-u www-data` no `exec`?** `docker compose exec` não passa pelo
> entrypoint, que é quem derruba o privilégio. Sem a flag, os arquivos criados
> dentro do container nasceriam pertencendo ao root. Em `run --rm` a flag não é
> necessária: o entrypoint cuida disso.

---

## 6. Qualidade

A ordem não é arbitrária: o Pint mexe em imports e tipos, então rodar o PHPStan
antes dele gera erro de análise que some sozinho depois.

```
1. pint --dirty       formata só o que mudou no git
2. rector --dry-run   revisa refactors sugeridos (LEIA o diff)
3. phpstan analyse    analisa o código já formatado — nível 8, sem baseline
4. pest               testa
```

```bash
make check      # roda os quatro; usa pint --test (não altera arquivo), o modo do CI
```

> **`make check` passa e o CI reprova?** Provavelmente é a cobertura. O `check`
> roda o Pest sem medir; o CI roda `pest --coverage --min=80` e **falha abaixo
> de 80%**. Antes de abrir o PR, rode `make test-coverage` — é o mesmo comando
> com o mesmo piso.

Testes rodam contra **SQLite em memória**, nunca contra o banco de
desenvolvimento. Se o projeto passar a depender de recurso específico do MySQL
(coluna JSON, fulltext, `ENUM`), o SQLite mente — nesse caso aponte
`DB_CONNECTION`/`DB_DATABASE` do `phpunit.xml` para `gestao_veiculo_testing`,
que já é criado no primeiro boot do MySQL.

Cobertura (usa PCOV, já instalado — **mínimo 80%**, o mesmo do CI):

```bash
make test-coverage
```

O piso de 80% é um chão, não uma meta: existe para a cobertura não cair um PR
por vez sem nada reclamar.

---

## 6a. API

A API REST fica em `/api/`. Rotas públicas (leitura) não exigem autenticação.
Escrita (store, update, destroy) exige token Sanctum.

```bash
# Listar veículos (público)
curl http://localhost:8000/api/vehicles

# Criar token para testes
make artisan c="tinker --execute=\"echo User::first()->createToken('test')->plainTextToken;\""

# Criar veículo (autenticado)
curl -X POST http://localhost:8000/api/vehicles \
  -H 'Authorization: Bearer <token>' \
  -H 'Content-Type: application/json' \
  -d '{"plate":"ABC-1234","brand":"Toyota","model":"Corolla","year":2024}'
```

Rate limit: 120 req/min autenticado, 20 req/min anônimo.

**Onde mexer quando as regras mudarem** — nunca no controller:

| Precisa mudar | Arquivo |
|---|---|
| Quais campos são aceitos e o que é válido | `app/Http/Requests/StoreVehicleRequest.php` e `UpdateVehicleRequest.php` |
| *Quem* pode criar, editar ou remover | `app/Policies/VehiclePolicy.php` |

Hoje a Policy responde "toda pessoa autenticada pode", porque o projeto ainda
não tem papéis. Quando o primeiro papel aparecer, a mudança é uma linha na
Policy e nenhuma no controller. Um teste de arquitetura em
`tests/Architecture/ArchTest.php` impede que validação volte para dentro do
controller. O raciocínio completo está no ADR de validação e autorização, em
[docs/adr/](docs/adr/README.md).

---

## 6b. Observabilidade

- **Pulse** (`/pulse`): dashboard de requests, jobs, exceções. Acesso restrito a
  usuários com `is_admin = true`.
- **Horizon** (`/horizon`): dashboard de filas Redis. Substitui o `queue:work`
  básico, com auto-balanceamento e retry. Mesmo gate de acesso.
- **Health check profundo** (`/up/deep`): retorna 200 com JSON se app, DB e
  Redis estão funcionais; 503 se qualquer um falhar. Use no load balancer.
- **Log estruturado**: em dev, log legível no stderr (`LOG_CHANNEL_STACK=stderr_pretty`).
  Em produção, JSON indexável (`stderr_json`).
- **Sentry**: captura exceções em produção. Requer DSN — veja §11.

---

## 6c. CI/CD

- **GitHub Actions** (`.github/workflows/ci.yml`): espelha o `make check` local
  nos mesmos containers, e ainda exige **cobertura ≥ 80%** (`pest --coverage
  --min=80`) — o único passo do CI que não está no `make check`.
- **Título do PR** (`.github/workflows/pr-title.yml`): valida o título em
  Conventional Commits. O hook `commit-msg` só vê commit local; no merge por
  squash, o título do PR é a única mensagem que sobrevive na master — e nenhum
  hook a alcança. A regex é a mesma do `captainhook.json`: mudou uma, muda a
  outra. Corrigir o título reexecuta o check sozinho.
- **CODEOWNERS** (`.github/CODEOWNERS`): pede review automaticamente nas áreas
  em que um erro não quebra uma tela, quebra o ambiente de todo mundo —
  `docker/`, `compose.yaml`, `.github/`, `config/`, `database/migrations/`,
  `app/Policies/`, `docs/adr/` e os arquivos de qualidade.
- **Dependabot** (`.github/dependabot.yml`): atualiza composer, npm, Docker
  images e GitHub Actions semanalmente.
- **CaptainHook** (`captainhook.json`): git hooks declarativos, Container First.
  - `pre-commit`: Pint, PHPStan, block secrets, conflict markers
  - `pre-push`: suíte completa (Pint + PHPStan + Pest)
  - `commit-msg`: valida Conventional Commits

Ativação (uma vez por clone):

```bash
make hook-install    # Windows: .\make.ps1 hook-install
```

---

## 7. Logs

```bash
make logs            # tudo
make logs-app        # app, nginx e queue
docker compose logs --tail=100 mysql vite scheduler
```

Os containers escrevem em stdout/stderr — não há arquivo de log escondido
dentro deles. O log da aplicação Laravel fica em `storage/logs/laravel.log`,
visível no host.

---

## 8. Resetar **apenas** o ambiente de desenvolvimento

Do menos para o mais destrutivo:

```bash
make down && make up               # recria containers, PRESERVA o banco
make rebuild                       # reconstrói as imagens do zero, PRESERVA o banco
make fresh   CONFIRM=yes           # APAGA E RECRIA AS TABELAS (roda os seeders)
make destroy CONFIRM=yes           # APAGA OS VOLUMES — o banco some junto
```

`fresh` e `destroy` recusam rodar sem `CONFIRM=yes`. Antes de `destroy`, faça
um dump:

```bash
make db-dump                       # grava dump-AAAAMMDD-HHMM.sql
```

---

## 9. Troubleshooting

**A página não reflete minha alteração de código.**
OPcache com `validate_timestamps=0` é o default de produção e faz exatamente
isso. Em dev o projeto usa `docker/php/conf.d/opcache-dev.ini`, com
revalidação imediata. Se o sintoma aparecer, confirme qual arquivo está ativo:

```bash
docker compose exec app php -i | grep opcache.validate_timestamps
```

**`SQLSTATE[HY000] [2002] Connection refused` na primeira migration.**
O `DB_HOST` precisa ser o **nome do serviço** (`mysql`), nunca `localhost` —
dentro de um container, `localhost` é o próprio container. O `compose.yaml` já
espera o healthcheck do MySQL antes de subir a aplicação.

**`404 File not found` em tudo.**
O nginx precisa do mesmo bind mount do código que o `app`. Confira o serviço
`nginx` no `compose.yaml`.

**O HMR não recarrega ao salvar arquivo.**
Duas causas, ambas cobertas em `vite.config.js`: `hmr.host` precisa ser um
endereço que o **navegador do host** alcance (`localhost`), e `usePolling`
precisa estar ligado, porque o inotify não propaga através de bind mount em
Windows/macOS/WSL2.

**O container `vite` fica reiniciando.**
Normal enquanto `node_modules` não existe — o serviço roda `npm run dev`. Por
isso toda instalação de frontend usa `run --rm`, não `exec`:

```bash
docker compose run --rm vite npm ci
```

**`EACCES: permission denied, open 'public/hot'`.**
Os containers PHP e Node gravam no mesmo bind mount e precisam do mesmo
uid/gid. Ambos usam `APP_UID`/`APP_GID` do `.env` — não crie um par separado
para o Node.

**Permissão negada em `storage/` ou `bootstrap/cache/` (Linux nativo).**
Alinhe `APP_UID`/`APP_GID` do `.env` com o seu usuário:

```bash
id -u && id -g
```

Depois `make rebuild`. Nunca use `chmod -R 777` em `storage/` — isso é falha de
segurança, não solução.

**A análise estática demora minutos.**
O cache do PHPStan e do Rector fica em `/tmp` dentro do container justamente
para não cair no bind mount. Se você mudou `tmpDir` no `phpstan.neon` para
dentro do projeto, reverta.

**Senha do banco chega truncada no container.**
O Compose interpola `${...}` no `.env`. Senha contendo `$` precisa de `$$` —
ou, mais simples, gere senhas sem `$`.

---

## 10. Notas específicas deste ambiente

- **Xdebug** vem carregado mas **desligado** (`XDEBUG_MODE=off`), porque ligado
  degrada cada request em 2–3×. Para depurar, mude `XDEBUG_MODE=debug` no
  `.env` e recrie o container `app`. Para cobertura de teste use PCOV, que já
  está instalado e é muito mais rápido.
- **Debugbar** está em `require-dev` e vem **desligada**
  (`DEBUGBAR_ENABLED=false`). Ligue só para depurar: a aba *Queries* é o
  detector de N+1, que é o problema de performance nº 1 em Laravel.
- **A porta 3306 não é publicada.** A aplicação fala `mysql:3306` pela rede
  interna e o acesso humano é pelo phpMyAdmin. Se precisar de um cliente
  externo, publique a porta no `compose.yaml` conscientemente.
- **Projeto em pasta sincronizada (OneDrive/Dropbox/Drive).** Funciona, mas o
  serviço de sync tenta acompanhar `storage/logs`, `storage/framework` e
  `public/build`. Se notar lentidão ou conflitos, mova o repositório para um
  caminho fora da pasta sincronizada.

---

## 11. Pendências de credencial externa

Estas credenciais são de **sistema externo** e não foram inventadas (§2.3).
Preencha no `.env` quando disponíveis:

| Variável | Onde obter | Impacto |
|---|---|---|
| `SENTRY_LARAVEL_DSN` | Sentry → Settings → Client Keys | Sem DSN, o Sentry não reporta. Sem erro. |
| `AWS_ACCESS_KEY_ID` | Console AWS → IAM | Sem credenciais S3, o backup não funciona. |
| `AWS_SECRET_ACCESS_KEY` | Console AWS → IAM | idem |
| `AWS_BUCKET` | Console AWS → S3 | idem |
