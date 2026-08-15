# Arquitetura e escopo do projeto

Documentação completa: o que existe dentro deste repositório, o que cada peça faz
e **por que** ela está aqui. Se você quer apenas colocar o projeto no ar, comece
pela **[INSTALACAO.md](INSTALACAO.md)**.

---

## Índice

1. [Visão geral](#1-visão-geral)
2. [Matriz de versões](#2-matriz-de-versões)
3. [Arquitetura de containers](#3-arquitetura-de-containers)
4. [Volumes e rede](#4-volumes-e-rede)
5. [Estrutura de diretórios](#5-estrutura-de-diretórios)
6. [A aplicação: camadas](#6-a-aplicação-camadas)
7. [Pacotes PHP (Composer)](#7-pacotes-php-composer)
8. [Pacotes JavaScript (npm)](#8-pacotes-javascript-npm)
9. [Ferramentas de qualidade](#9-ferramentas-de-qualidade)
10. [CaptainHook — os git hooks](#10-captainhook--os-git-hooks)
11. [Observabilidade](#11-observabilidade)
12. [API](#12-api)
13. [Produção](#13-produção)
14. [CI/CD](#14-cicd)
15. [Decisões de arquitetura](#15-decisões-de-arquitetura)
16. [Armadilhas conhecidas](#16-armadilhas-conhecidas)

---

## 1. Visão geral

Aplicação **Laravel 13** para gestão de frota de veículos, **integralmente
containerizada**. O objetivo do repositório não é só "rodar um Laravel": é
entregar um ambiente que já nasce com observabilidade, API, testes, análise
estática, gates de commit e pipeline de CI.

### O princípio que organiza tudo: *Container First*

**Nenhuma ferramenta de desenvolvimento é instalada na máquina do desenvolvedor.**
PHP, Composer, Node, npm, MySQL e Redis existem **apenas dentro dos containers**.
Na máquina entram só Docker, Git e um editor.

Consequência prática — todo comando passa pelo Docker:

```bash
docker compose run --rm app  composer require vendor/pacote
docker compose run --rm vite npm install alguma-lib
docker compose exec -u www-data app php artisan migrate
```

Isso existe para eliminar a classe inteira de bug do "na minha máquina funciona":
a versão do PHP, as extensões carregadas e a versão do MySQL são idênticas para
todo mundo, inclusive para o CI.

Há uma auditoria automatizada disso em `scripts/audit-container-first.sh`, que
falha se aparecer comando de host na documentação.

### A fatia de aplicação é demonstrativa

O domínio de frota (`Vehicle`, `VehicleStatus`, `ListVehicles`…) existe para
**provar que a infraestrutura funciona de ponta a ponta**: um teste real, um job
processado pela fila, Bootstrap e Tom Select renderizando no navegador, uma API
devolvendo JSON. Quando o domínio real do projeto começar, substitua essa fatia —
ela não pretende ser o produto.

---

## 2. Matriz de versões

| Componente | Versão | Onde é fixado |
|---|---|---|
| PHP | 8.4.24 | `docker/php/Dockerfile` (`PHP_VERSION`) |
| Laravel Framework | 13.25.0 | `composer.json` |
| MySQL | 8.4 (LTS) | `docker/mysql/Dockerfile` |
| Redis | 8-alpine | `compose.yaml` |
| Nginx | 1.29-alpine | `docker/nginx/Dockerfile` |
| Node | 24 | `docker/node/Dockerfile` (`NODE_VERSION`) |
| Composer | 2 | multi-stage `composer:2` |
| Pest | 5.1.1 | `composer.json` (dev) |
| PHPUnit | 13.3.0 | exigido pelo Pest 5 |
| Larastan / PHPStan | 3.10.0 / 2.x | `composer.json` (dev) |
| Rector | 2.6.2 | `composer.json` (dev) |
| Bootstrap | 5.3.8 | `package.json` |
| Tom Select | 2.6.2 | `package.json` |
| Vite | 8.x | `package.json` (dev) |

**Extensões PHP compiladas:** `bcmath`, `intl`, `opcache`, `pcntl`, `pdo_mysql`,
`zip`, `redis` (PECL). No stage `dev` entram também `pcov` (cobertura, rápida) e
`xdebug` (step debugging, carregado mas com `mode=off`).

---

## 3. Arquitetura de containers

```
rede: gestao-veiculo-ppw_net (bridge)

  navegador ──:8000──▶ nginx ──fastcgi :9000──▶ app (php-fpm)
      │                  ▲                          │
      │  :5175 HMR       │ mesmo bind mount         ├──▶ mysql :3306  [volume]
      ▼                  │ do código                └──▶ redis :6379  [volume]
     vite ───────────────┘                                 ▲
                                                  queue ───┤ php artisan horizon
  profile "tools":                             scheduler ──┘ php artisan schedule:work
    phpmyadmin :8082    mailpit :8026
```

São **9 serviços**. Os quatro primeiros são a aplicação; os dois seguintes são
processos de fundo; os três últimos são apoio.

### `app` — PHP-FPM

O interpretador PHP. Não fala HTTP: recebe requisições via **FastCGI** na porta
9000, vindas do nginx. É por isso que ele não publica porta para o host.

- **Imagem:** construída de `docker/php/Dockerfile` (stage `dev`)
- **Healthcheck:** `php-fpm-healthcheck` bate no endpoint `/fpm-ping` interno
- **Usuário:** o processo *master* roda como root; os *workers* (que executam seu
  código) rodam como `www-data`

> **Por que o master é root?** Rodando como não-root o FPM não consegue abrir
> `/proc/self/fd/2` e aborta no boot. Como o código da aplicação roda nos
> *workers*, o privilégio elevado não alcança o código. Qualquer comando que não
> seja `php-fpm` é rebaixado pelo entrypoint.

### `nginx` — servidor web

Recebe o HTTP do navegador, entrega arquivo estático direto e repassa o resto ao
PHP-FPM.

- **Porta:** `8000:80` (configurável por `APP_PORT`)
- **Config:** `docker/nginx/conf.d/app.conf`
- **Healthcheck:** `curl /nginx-health`, que **não** depende do PHP — assim você
  distingue "nginx caiu" de "aplicação caiu"

O bind mount do código está no nginx **também**, não só no `app`. O nginx precisa
enxergar o arquivo para montar o `SCRIPT_FILENAME` que envia ao FPM; se o código
existisse só no `app`, toda request viraria `404 File not found`.

### `mysql` — banco de dados

MySQL 8.4 LTS. **Não publica porta para o host** — quem precisa de interface
gráfica usa o phpMyAdmin.

- **Volume:** `mysql_data` (os dados sobrevivem a `make down`)
- **Healthcheck:** um `SELECT 1` real contra o banco da aplicação, não apenas
  "o processo está vivo"
- **Init:** `docker/mysql/init/01-testing-db.sh` cria o banco `gestao_veiculo_testing`
  no primeiro boot

### `redis` — cache, sessão e fila

Serve a três papéis: driver de **cache**, de **sessão** e de **fila**.

- **Volume:** `redis_data`
- Porta não publicada; rede bridge privada

### `vite` — servidor de front-end (só desenvolvimento)

Compila SCSS/JS e faz **HMR** (*hot module replacement*): você salva o arquivo e o
navegador atualiza sozinho.

- **Porta:** `5175`, publicada só em `127.0.0.1`
- Em produção este container **não existe** — lá se usa o resultado de
  `npm run build`, arquivos estáticos servidos pelo nginx

### `queue` — worker da fila (Horizon)

Roda `php artisan horizon`, que consome os jobs enfileirados no Redis.

> Este serviço roda **Horizon**, não `queue:work` cru. Os dois brigariam pela mesma
> fila se subissem juntos — por isso o comando foi substituído, não somado.

### `scheduler` — agendador

Roda `php artisan schedule:work`, substituindo a entrada de cron do host. É ele
que dispara as tarefas de `routes/console.php` (resumo da frota a cada 5 min,
backups diários).

### `phpmyadmin` e `mailpit` — apoio (profile `tools`)

- **phpMyAdmin** (`:8082`) — interface web do banco
- **Mailpit** (`:8026`) — captura **todo** e-mail que a aplicação enviar, para que
  nada escape para um endereço real em desenvolvimento

Ambos sobem porque o `.env` traz `COMPOSE_PROFILES=tools`. Deixe a variável vazia
para não subi-los.

---

## 4. Volumes e rede

### Volumes nomeados

| Volume | Guarda |
|---|---|
| `mysql_data` | Os dados do banco |
| `redis_data` | Persistência do Redis |
| `mailpit_data` | E-mails capturados |
| `vendor` | Dependências PHP |
| `node_modules` | Dependências JS |

> **Por que `vendor/` e `node_modules/` são volumes, e não bind mount?** São
> dezenas de milhares de arquivos pequenos. Sincronizá-los com o host no Docker
> Desktop é lento a ponto de inviabilizar o dia a dia. Como volume, eles vivem no
> disco do Docker e **não precisam existir na sua máquina**.

`make down` preserva tudo isso. Só `make destroy CONFIRM=yes` apaga.

### Rede

Uma bridge privada, `gestao-veiculo-ppw_net`. Os containers se enxergam **pelo
nome do serviço** (`app`, `mysql`, `redis`) — é por isso que o `.env` traz
`DB_HOST=mysql` e não um IP.

Só três portas saem para o host, e duas delas apenas em `127.0.0.1`. MySQL e Redis
não são alcançáveis de fora.

---

## 5. Estrutura de diretórios

### O que veio do Laravel

`app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`,
`storage/`, `tests/`, `artisan`, `composer.json`, `vite.config.js`.

Alguns arquivos padrão foram alterados — as mudanças estão marcadas em
[Decisões de arquitetura](#15-decisões-de-arquitetura).

### O que foi criado para este projeto

```
compose.yaml                  Orquestra os 9 serviços
Makefile                      Atalhos (Linux/macOS/WSL)
make.ps1                      Equivalente para PowerShell — Windows não traz make
captainhook.json              Configuração dos git hooks
pint.json                     Regras de formatação
phpstan.neon                  Configuração da análise estática
rector.php                    Regras de refactor automatizado
.dockerignore                 O que não entra no contexto de build
scripts/
  audit-container-first.sh    Falha se houver comando de host na documentação
docker/
  php/
    Dockerfile                Multi-stage: base → dev / prod
    entrypoint.sh             Ajusta permissões e rebaixa privilégio
    healthcheck.sh            Checagem do FPM
    conf.d/app.ini            Configuração base do PHP
    conf.d/opcache-dev.ini    OPcache revalidando (dev)
    conf.d/opcache-prod.ini   OPcache travado + preload (prod)
    conf.d/xdebug.ini         Xdebug, desligado por padrão
    fpm/www.conf              Pool do PHP-FPM
  nginx/
    Dockerfile
    conf.d/app.conf           Virtual host + resolver DNS
  mysql/
    Dockerfile
    conf.d/my.cnf             Tuning do MySQL
    init/01-testing-db.sh     Cria o banco de teste no primeiro boot
  node/
    Dockerfile
    entrypoint.sh
  captainhook/
    githook.sh                Ponte host → container dos git hooks
docs/
  ARQUITETURA.md              Este documento
  INSTALACAO.md               Guia de instalação
.github/
  workflows/ci.yml            Pipeline de CI
  dependabot.yml              Atualização automática de dependências
```

---

## 6. A aplicação: camadas

O código não vive todo dentro do controller. A separação é intencional e há
**arch tests** que a mantêm (`tests/Architecture/ArchTest.php`) — se alguém
quebrar a regra, a suíte falha.

```
Request
   │
   ▼
Controller ──────▶ Action ──────▶ Model ──────▶ Banco
   │  (HTTP)        (regra)       (dados)
   │
   ├──▶ Resource   (formata o JSON da API)
   └──▶ View       (Blade, para o navegador)

Job ────▶ Action     (mesma regra, agora em background)
Command ▶ Job        (agenda o trabalho)
```

### `app/Models/` — dados

`Vehicle` e `User`. `Vehicle` faz *cast* da coluna `status` para o enum
`VehicleStatus`, então o resto do código nunca lida com a string crua.

### `app/Enums/VehicleStatus.php` — estados possíveis

Enum de string com três casos (`disponivel`, `em_uso`, `manutencao`) e dois
métodos: `label()` (rótulo legível) e `badgeClass()` (classe do badge Bootstrap).

Concentrar isso no enum evita `if` de status espalhado por view e controller. Há
arch test garantindo que enums não carreguem estado.

### `app/Actions/` — regra de negócio

`ListVehicles` (lista, com filtro opcional) e `SummariseFleet` (agrega por status).

Cada Action é `final`, tem **um único** ponto de entrada — o método `handle()` — e
**não depende de facade** (as duas regras são cobertas por arch test). Isso as
torna testáveis isoladamente e reaproveitáveis: a mesma `SummariseFleet` é usada
pelo job e poderia ser usada por um controller.

### `app/Http/Controllers/` — entrada HTTP

- `VehicleController` — página web (devolve Blade)
- `Api/VehicleApiController` — API (devolve `VehicleResource`, **nunca** o Model)

Controllers concretos são `final` e estendem o `Controller` base — arch test cobre.

### `app/Http/Resources/` — contrato da API

`VehicleResource` e `UserResource` listam **campo a campo** o que sai no JSON.

> Essa é a barreira que impede vazamento de dado. `return User::all()` devolveria
> toda coluna da tabela — inclusive as que alguém adicionar amanhã sem pensar na
> API. O Resource torna a exposição uma decisão explícita.

### `app/Jobs/BuildFleetSummary.php` — trabalho em background

Chama `SummariseFleet` e grava o resultado em cache. Enfileirado no Redis,
processado pelo Horizon. Arch test exige que jobs sejam `final` e enfileiráveis.

### `app/Console/Commands/FleetSummaryCommand.php`

Comando artisan que despacha o job. É o que o `scheduler` chama a cada 5 minutos.

### `app/Providers/`

- `AppServiceProvider` — registra o gate `viewPulse` e os *rate limiters*
- `HorizonServiceProvider` — registra o gate `viewHorizon`

### `app/Support/SentryBeforeSend.php`

Filtro aplicado a todo evento antes de ir ao Sentry. É uma **classe**, não uma
closure, por um motivo concreto: `config:cache` serializa a configuração com
`var_export`, que não sabe representar closure — o build de produção quebrava com
`Call to undefined method Closure::__set_state()`.

### `resources/`

- `css/app.scss` — importa o Bootstrap e os estilos do projeto
- `js/app.js` — ponto de entrada; carrega Bootstrap e os componentes
- `js/components/tom-select.js` — inicializa o Tom Select nos elementos marcados
  com `data-tom-select`
- `views/layouts/app.blade.php` — layout base
- `views/vehicles/index.blade.php` — listagem da frota

---

## 7. Pacotes PHP (Composer)

### Produção (`require`)

Esta lista é curta **de propósito**: nenhuma ferramenta de qualidade ou debug entra
aqui, porque nada disso deve ir para o servidor.

| Pacote | O que é / para que serve |
|---|---|
| **laravel/framework** | O framework. |
| **laravel/tinker** | REPL (`php artisan tinker`): abre um console PHP com a aplicação carregada, para inspecionar dados e testar trechos de código. |
| **laravel/pulse** | Dashboard de performance em `/pulse`: requisições lentas, queries lentas, exceções, uso de fila. Grava em tabelas próprias no MySQL. |
| **laravel/horizon** | Painel e supervisor das filas Redis em `/horizon`: quantos jobs rodando, throughput, falhas, retentativa. Substitui o `queue:work` cru. |
| **laravel/sanctum** | Autenticação da API. Dois modos: *cookie* (SPA no mesmo domínio) e *token* (app mobile, integração externa). |
| **sentry/sentry-laravel** | Captura exceções de produção e envia para o painel do Sentry, com stack trace e contexto. |
| **spatie/laravel-backup** | Backup do banco e dos arquivos, com limpeza e monitoramento agendados. |
| **league/flysystem-aws-s3-v3** | Driver S3. É o que faz o disco `s3` existir de verdade — sem ele, o destino de backup não funcionaria. |

### Desenvolvimento (`require-dev`)

| Pacote | O que é / para que serve |
|---|---|
| **pestphp/pest** | Framework de testes. Sintaxe enxuta (`it('...', fn () => ...)`) por cima do PHPUnit. |
| **pestphp/pest-plugin-laravel** | Helpers do Laravel dentro do Pest (`get()`, `actingAs()`, `assertDatabaseHas()`). |
| **phpunit/phpunit** | Motor que roda por baixo do Pest. |
| **larastan/larastan** | PHPStan com conhecimento do Laravel — entende Eloquent, facades e container. Roda em **nível 8**, sem baseline. |
| **rector/rector** | Refactor automatizado: moderniza sintaxe e aponta código morto. |
| **driftingly/rector-laravel** | Regras de Rector específicas do Laravel. |
| **laravel/pint** | Formatador de código (PSR-12 + preset Laravel). Fim das discussões de estilo em code review. |
| **barryvdh/laravel-debugbar** | Barra de debug no navegador: queries executadas, tempo, memória, rotas. |
| **laravel/pail** | `php artisan pail` — acompanha os logs da aplicação em tempo real, formatados. |
| **laravel/pao** | Saída de teste otimizada para leitura por agente/IA. |
| **nunomaduro/collision** | Deixa o erro de CLI legível: stack trace colorido, com trecho do código. |
| **fakerphp/faker** | Gera dados falsos para factories e seeders. Configurado em `pt_BR`. |
| **mockery/mockery** | Cria *mocks* para isolar dependências nos testes. |
| **captainhook/captainhook** | Gerenciador de git hooks — veja a [seção 10](#10-captainhook--os-git-hooks). |
| **captainhook/hook-installer** | Plugin do Composer que reinstala os hooks a cada `composer install`, para que ninguém do time fique sem eles. |

### Dependências transitivas que aparecem sem você pedir

- **livewire/livewire** — vem junto do Pulse; é a tecnologia do dashboard dele
- **captainhook/secrets** — biblioteca de detecção de segredos usada pelo CaptainHook
- **nesbot/carbon** — manipulação de datas, base do framework

---

## 8. Pacotes JavaScript (npm)

### Produção (`dependencies`)

| Pacote | O que é / para que serve |
|---|---|
| **bootstrap** (5.3) | Framework CSS: grid, componentes, utilitários. É o que dá a aparência da interface. Foi a escolha do projeto no lugar do Tailwind. |
| **tom-select** (2.6) | Transforma um `<select>` comum em campo com busca, seleção múltipla e tags. Usado nos filtros da frota; é ativado nos elementos com `data-tom-select`. Substitui o Select2 sem depender de jQuery. |

### Desenvolvimento (`devDependencies`)

| Pacote | O que é / para que serve |
|---|---|
| **vite** (8) | Empacotador. Em dev serve os arquivos com HMR; em build gera CSS/JS minificados com hash no nome. |
| **laravel-vite-plugin** | Liga o Vite ao Laravel: é o que faz a diretiva `@vite(...)` do Blade encontrar o arquivo certo, em dev e em produção. |
| **sass-embedded** | Compilador Sass — necessário porque os estilos estão em `.scss`. |

> **Onde está o Tailwind?** O skeleton do Laravel 13 vem com Tailwind 4. Ele foi
> **removido**: o alvo visual deste projeto é o Bootstrap 5, e manter os dois seria
> carregar dois frameworks CSS concorrentes no mesmo bundle.

> **Onde está o jQuery?** Não existe. O Bootstrap 5 não depende mais dele e o Tom
> Select também não.

---

## 9. Ferramentas de qualidade

O pipeline completo é `make check`, e a **ordem importa**:

```
1. pint      formata
2. rector    revisa refactors (dry-run — nunca aplica sozinho)
3. phpstan   analisa o código já formatado
4. pest      testa
```

> Rodar o PHPStan **antes** do Pint gera erro de análise que some sozinho depois
> da formatação — daí a ordem.

### Pint — formatação

```bash
make lint       # verifica, não altera (é o modo do CI)
make lint-fix   # corrige o que mudou no git
```

### Rector — refactor assistido

```bash
make rector         # mostra o diff, não aplica
make rector-apply   # aplica — leia o diff antes
```

**Rector aplicando sozinho no CI seria receita de desastre**, por isso o CI só roda
`--dry-run`.

### PHPStan / Larastan — análise estática

Nível **8** (o mais alto útil), **sem baseline**.

> Baseline é dívida registrada para código legado. Num projeto que nasceu agora,
> baseline vira lixeira para erro recém-escrito. Os únicos `ignoreErrors` são
> falsos positivos do Pest e arquivos publicados por pacote (Pulse, Horizon,
> Sanctum), cada um preso a um caminho específico. E `reportUnmatchedIgnoredErrors`
> está ligado: no dia em que um ignore deixar de ser necessário, a análise falha e
> ele é removido.

### Pest — testes

```bash
make test            # roda a suíte
make test-coverage   # com cobertura (PCOV), mínimo 80%
```

**32 testes** divididos em:

- **Unit** — enum de status
- **Feature** — página da frota, API, job da fila, health check
- **Architecture** — as regras estruturais descritas na [seção 6](#6-a-aplicação-camadas)

Os arch tests também cobrem regras de segurança: sem `dd()`/`dump()` esquecido, sem
`env()` fora de `config/`.

> **Por que "sem `env()` fora de `config/`" importa:** `php artisan config:cache`,
> que roda no build de produção, **congela** o `env()`. Qualquer chamada fora de um
> arquivo de config passa a devolver `null` em produção — e o bug só aparece lá.

---

## 10. CaptainHook — os git hooks

### O que é

Gerenciador de **git hooks**: scripts que o Git dispara sozinho em momentos-chave
(antes do commit, antes do push, ao validar a mensagem). É o equivalente PHP do
Husky do mundo JS, com uma vantagem decisiva aqui: **modo Docker nativo**.

### Por que existe

Para que erro seja barrado **antes** de virar commit. Sem isso, o CI vira o único
guarda — e você só descobre o problema depois do push, num ciclo lento.

### Como ele mantém o Container First

O Git roda no **host**, mas quem processa o hook é o **container**. A ponte é
`docker/captainhook/githook.sh` — **bash puro, sem uma linha de PHP**:

```bash
docker compose up --no-recreate -d app >/dev/null 2>&1
exec docker compose exec -T -w /var/www/html app "$@"
```

Assim os hooks funcionam mesmo numa máquina que não tem PHP instalado — que é
justamente o caso de todo mundo neste projeto.

### O que cada hook faz

| Hook | Ações |
|---|---|
| **pre-commit** | Bloqueia commit direto em `main`/`master`/`develop`; roda Pint e PHPStan **só se houver `.php` no stage**; barra arquivo acima de 5 MB; detecta segredo (chave AWS, token GitHub/Google, senha); barra marcador de conflito de merge (`<<<<<<<`) esquecido |
| **pre-push** | Bloqueia push direto em `main`/`master`; `composer validate --strict`; Pint; PHPStan; **suíte inteira** com `pest --parallel` |
| **commit-msg** | Exige [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `docs:`…) |
| **post-checkout** / **post-merge** | Rodam `composer install` **apenas se o `composer.lock` mudou** |

Notas de projeto:

- O **pre-commit é condicional** — um commit que só toca `README.md` não paga o
  custo de rodar análise estática
- O **pre-push é mais pesado** porque roda menos vezes
- Não existe ação nativa de "proteger branch" no CaptainHook: a proteção é a
  combinação da condição `OnMatchingBranch` com uma ação que sempre falha

### Ativação

Uma vez por clone:

```bash
make hook-install
```

O `captainhook/hook-installer` reexecuta isso a cada `composer install`, então
ninguém do time fica sem os hooks por esquecimento.

---

## 11. Observabilidade

### Pulse — `/pulse`

Dashboard de saúde da aplicação: requisições e queries lentas, exceções, uso de
fila, usuários ativos. Grava em tabelas próprias no MySQL.

**Protegido pelo gate `viewPulse`** (exige `is_admin`). Isso não é opcional: o
painel expõe query lenta, exceção e volume de tráfego — informação de operação
interna.

### Horizon — `/horizon`

Painel das filas Redis: jobs em execução, throughput, falhas, retentativa manual.
Protegido pelo gate `viewHorizon`, mesmo critério.

### Sentry

Captura exceção de produção com stack trace e contexto. Ligado ao Laravel via
`Integration::handles($exceptions)` em `bootstrap/app.php`.

Configuração relevante:

- `ignore_exceptions` — ignora `AuthenticationException` e `ValidationException`
  (401 e 422 são comportamento esperado, não bug)
- `before_send` — filtro em `app/Support/SentryBeforeSend.php`
- `SENTRY_TRACES_SAMPLE_RATE=0` em desenvolvimento

> **Por que a amostragem é 0 em dev:** com 1.0, **cada teste do Pest vira um evento
> no Sentry** e o plano gratuito estoura em horas.

⚠️ **Pendência:** `SENTRY_LARAVEL_DSN` está vazio. O Sentry está instalado e
configurado, mas só reporta de fato quando o DSN for preenchido. Nenhuma
credencial foi inventada.

### Health checks — dois níveis

| Rota | Responde |
|---|---|
| `/up` | "O PHP está de pé" (nativa do Laravel) |
| `/up/deep` | "A aplicação está funcional": app + **banco** + **Redis** |

`/up/deep` devolve `200` com `{"app":true,"db":true,"redis":true}`, ou **`503`** se
qualquer dependência cair.

> **Por que a rota profunda existe:** um load balancer confiando só no `/up`
> continuaria mandando tráfego para uma instância com o MySQL fora do ar — o PHP
> responde, mas a aplicação não funciona. Comportamento verificado na prática:
> parando o container do MySQL, a rota passa a devolver 503.

### Log estruturado

| Ambiente | Canal | Formato |
|---|---|---|
| Desenvolvimento | `stderr_pretty` | Texto legível |
| Produção | `stderr_json` | JSON indexável |

Controlado por `LOG_CHANNEL_STACK` no `.env`.

> **Container nunca escreve log em arquivo dentro de si mesmo** — o filesystem do
> container é efêmero e some junto com ele. O log vai para stdout/stderr; quem
> persiste e indexa é a infraestrutura de fora (Loki, CloudWatch, Datadog).

---

## 12. API

### Autenticação — Sanctum

Configurado nos **dois modos**:

- **Cookie** (SPA no mesmo domínio) — via `SANCTUM_STATEFUL_DOMAINS` e o middleware
  `statefulApi()`
- **Token** (mobile, integração externa) — `$user->createToken('nome')->plainTextToken`

> O token é exibido **uma única vez**. Perdeu, gera outro — não há como recuperar.

### Rotas

| Método | Rota | Autenticação |
|---|---|---|
| `GET` | `/api/vehicles` | Pública |
| `GET` | `/api/vehicles/{id}` | Pública |
| `POST` | `/api/vehicles` | Sanctum |
| `PUT/PATCH` | `/api/vehicles/{id}` | Sanctum |
| `DELETE` | `/api/vehicles/{id}` | Sanctum |
| `GET` | `/api/user` | Sanctum |

As rotas de API usam o prefixo de nome `api.vehicles.*` para não colidir com a rota
web `vehicles.index` — colisão de nome faz `route:cache` falhar no build.

### Rate limiting

| Perfil | Limite |
|---|---|
| Autenticado | 120 req/min (por usuário) |
| Anônimo | 20 req/min (por IP) |
| Login | 5 req/min (por IP + e-mail) |

Aplicado por `throttleApi()` em `bootstrap/app.php`. As respostas trazem os
cabeçalhos `X-RateLimit-Limit` e `X-RateLimit-Remaining`; ao estourar, `429`.

> **Rota de autenticação sem limite é a porta mais barata para força bruta** — por
> isso o limiter de login existe mesmo antes de haver tela de login.

---

## 13. Produção

### Caches gerados no build, não no boot

O stage `prod` do Dockerfile roda, **durante o build da imagem**:

```dockerfile
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache
```

> **Por que no build:** gerando em runtime, o primeiro request de cada container
> novo paga o custo de compilar tudo — e se dois containers sobem ao mesmo tempo,
> disputam o mesmo arquivo de cache.

O stage `prod` também não instala dependência de desenvolvimento, trava o OPcache
(sem revalidação) e não tem Xdebug nem bash.

### Backup

`spatie/laravel-backup`, apontando para o disco **`s3`** — nunca `local`.

> Backup no disco local do container é backup que morre junto com o container.

Agendado em `routes/console.php`:

```php
Schedule::command('backup:run')->dailyAt('03:00');
Schedule::command('backup:clean')->dailyAt('04:00');
Schedule::command('backup:monitor')->dailyAt('05:00');
```

> `backup:monitor` é o item que quase todo mundo esquece. Sem ele, um backup que
> silenciosamente parou de rodar só é descoberto no dia em que alguém precisa
> restaurar — e não há o que restaurar.

⚠️ **Pendência:** `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` e `AWS_BUCKET` estão
vazios. O backup não roda até serem preenchidos. Nenhuma credencial foi inventada.

---

## 14. CI/CD

### GitHub Actions — `.github/workflows/ci.yml`

Dispara em pull request e em push para `master`/`main`. Os passos:

1. Copia `.env.example` e gera credenciais efêmeras de CI
2. Constrói as imagens
3. Sobe apenas MySQL e Redis (nginx, vite e queue não são necessários para
   teste/lint — economiza tempo)
4. Instala dependências PHP e JS
5. `composer audit` + `npm audit` (**auditoria de segurança**)
6. Pint → Rector (dry-run) → PHPStan → Pest → build do front-end

> **O CI roda os mesmos comandos, nos mesmos containers, que você roda no
> `make check`.** Se o CI usasse comando diferente, ele viraria uma cópia infiel —
> passando quando o local falha, ou o contrário — e a confiança no pipeline
> desmoronaria.

### Dependabot — `.github/dependabot.yml`

Abre PR semanal para Composer, npm, **os quatro Dockerfiles** e as próprias
GitHub Actions. Pacotes Laravel e ferramentas de qualidade vêm agrupados, para não
gerar dez PRs isolados.

---

## 15. Decisões de arquitetura

| Decisão | Motivo |
|---|---|
| **Tailwind removido** | O alvo é Bootstrap 5; manter os dois seria carregar dois frameworks CSS concorrentes. |
| **Horizon no lugar de `queue:work`** | Dá painel, métricas e balanceamento. Os dois juntos brigariam pela mesma fila. |
| **PHPStan nível 8 sem baseline** | Projeto novo não tem legado a registrar; baseline aqui viraria lixeira. |
| **Rector nunca aplica no CI** | Refactor automático sem revisão humana é desastre. Só `--dry-run`. |
| **Caches do PHPStan/Rector em `/tmp` do container** | Em `storage/` (bind mount + OneDrive) a análise passava de 10 minutos; em `/tmp`, ~52 s. |
| **`bash` só no stage `dev`** | `docker exec -it ... bash` é o reflexo de todo mundo, e a base Alpine só tem `sh`. A imagem de produção continua enxuta. |
| **`container_name` com prefixo escolhido pelo usuário** | O nome gerado pelo Compose (`gestao-veiculo-ppw-app-1`) é longo demais para digitar num `docker exec`. O prefixo vem de `CONTAINER_PREFIX` no `.env` e é **livre**; sem ele, cai em `COMPOSE_PROJECT_NAME` e depois no nome da pasta — o padrão do Docker. Custo: nome de container é único por host, então duas cópias simultâneas exigem prefixos diferentes. |
| **`resolver` DNS no nginx** | Com hostname literal, o nginx cacheia o IP no boot e devolve 502 depois de recriar o `app`. Com variável + resolver, ele re-resolve a cada request. |
| **`before_send` como classe, não closure** | `config:cache` usa `var_export`, que não serializa closure — quebrava o build de produção. |
| **Rotas de API com prefixo `api.vehicles.*`** | O nome padrão colidia com a rota web `vehicles.index` e fazia `route:cache` falhar. |
| **`APP_TIMEZONE` removido do `.env`** | O `config/app.php` do Laravel 13 fixa `'UTC'` e **não lê** essa variável. Mantê-la faria o `.env` mentir sobre o fuso da aplicação. |
| **`compose.override.yaml` não versionado** | O propósito dele é customização pessoal do desenvolvedor. |
| **`make.ps1` além do `Makefile`** | O Windows não traz `make`, e instalá-lo contrariaria "só Docker, Git e um editor". |

---

## 16. Armadilhas conhecidas

### Nunca use `$` nas senhas do `.env`

O Docker Compose interpreta `$` como início de variável e a senha chega truncada
dentro do container. Use apenas letras e números (`openssl rand -hex 16`).

### `docker compose exec` entra como root

O `exec` **não** passa pelo entrypoint, que é quem rebaixa o privilégio. Sem
`-u www-data`, todo arquivo criado nasce pertencendo ao root e você não consegue
editá-lo pelo editor sem `sudo`. Prefira `make shell`.

Em `docker compose run --rm` a flag não é necessária — ali o entrypoint roda.

### Projeto dentro de pasta sincronizada (OneDrive/Google Drive)

Funciona, mas o serviço de sincronização acompanha `storage/`, `public/build` e
afins, gerando lentidão e conflito. A recomendação é o WSL2 —
veja a [INSTALACAO.md](INSTALACAO.md).

### Testes rodam em SQLite

É rápido, mas **SQLite mente** se o projeto passar a depender de recurso específico
do MySQL (coluna JSON, fulltext, `ENUM`). Nesse caso aponte `DB_CONNECTION` e
`DB_DATABASE` do `phpunit.xml` para `gestao_veiculo_testing`, que já é criado no
primeiro boot do MySQL.

### Redis sem senha

Aceitável **em desenvolvimento**: a porta não é publicada e a rede é uma bridge
privada. **Não replique isso em produção.**

### Avisos "normais" no log

- MySQL: `CA certificate is self signed` e `insecure --pid-file` — comportamento da
  imagem oficial, não da configuração deste projeto
- Entrypoint: `chmod em storage/ não teve efeito` — esperado em bind mount do
  Docker Desktop

---

## Pendências de credencial externa

Nenhuma credencial de sistema externo foi inventada. Preencha no `.env` quando
tiver acesso:

| Variável | Onde obter | O que trava sem ela |
|---|---|---|
| `SENTRY_LARAVEL_DSN` | Sentry → Settings → Client Keys | Sentry não reporta (sem erro) |
| `AWS_ACCESS_KEY_ID` | Console AWS → IAM | Backup não funciona |
| `AWS_SECRET_ACCESS_KEY` | Console AWS → IAM | idem |
| `AWS_BUCKET` | Console AWS → S3 | idem |

---

## Fora de escopo

Deliberadamente **não** incluídos: Laravel Octane, filas com Swoole, multi-região,
feature flags (Pennant).

> Esses itens só compensam a complexidade quando existe gargalo real **medido**.
> Adicionar antes disso é o mesmo erro de instalar uma linguagem inteira "porque
> pode ser útil".
