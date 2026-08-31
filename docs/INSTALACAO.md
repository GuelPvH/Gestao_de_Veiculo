# Instalação passo a passo — Windows + WSL2 + Docker

Guia completo para sair do zero e rodar o projeto. Escrito para **Windows 10/11**,
com as observações de Linux e macOS no final.

> **A regra que vale para tudo neste projeto:** você **não instala PHP, Composer,
> Node, npm nem MySQL na sua máquina**. Nada disso. Tudo roda dentro de container.
> Na sua máquina entram apenas **Docker Desktop**, **Git** e um **editor**.
> Se um tutorial mandar você instalar PHP no Windows, ele não é deste projeto.

---

## Índice

1. [O que você precisa instalar](#1-o-que-você-precisa-instalar)
2. [Passo 1 — Instalar o WSL2 (Ubuntu)](#2-passo-1--instalar-o-wsl2-ubuntu)
3. [Passo 2 — Instalar o Docker Desktop](#3-passo-2--instalar-o-docker-desktop)
4. [Passo 3 — Ligar a integração Docker ↔ WSL](#4-passo-3--ligar-a-integração-docker--wsl)
5. [Passo 4 — Baixar o projeto](#5-passo-4--baixar-o-projeto)
6. [Passo 5 — Configurar o `.env`](#6-passo-5--configurar-o-env)
7. [Passo 6 — Subir o projeto](#7-passo-6--subir-o-projeto)
8. [Passo 7 — Conferir se está tudo no ar](#8-passo-7--conferir-se-está-tudo-no-ar)
9. [Entrar dentro do container](#9-entrar-dentro-do-container)
10. [Comandos do dia a dia](#10-comandos-do-dia-a-dia)
11. [Parar, religar e desinstalar](#11-parar-religar-e-desinstalar)
12. [Troubleshooting](#12-troubleshooting)
13. [Linux e macOS](#13-linux-e-macos)

---

## 1. O que você precisa instalar

| Software | Para quê | Onde baixar |
|---|---|---|
| **WSL2 + Ubuntu** | Sistema de arquivos Linux nativo. É o que faz o projeto rodar rápido. | Via PowerShell (Passo 1) |
| **Docker Desktop** | Executa os containers. | <https://www.docker.com/products/docker-desktop/> |
| **Git** | Baixar o código. Já vem dentro do Ubuntu do WSL. | Já incluso |
| **VS Code** (recomendado) | Editar o código. | <https://code.visualstudio.com/> |

**Requisitos da máquina:** Windows 10 versão 2004+ ou Windows 11, 8 GB de RAM
(16 GB confortável), ~10 GB livres em disco e virtualização habilitada na BIOS
(quase sempre já vem ligada).

---

## 2. Passo 1 — Instalar o WSL2 (Ubuntu)

### Por que o WSL2 e não direto no Windows

O Docker precisa ler os arquivos do projeto a cada request. Quando o projeto fica
em `C:\Users\...`, cada leitura atravessa a fronteira Windows↔Linux, e isso deixa
`phpstan`, os testes e o *hot reload* do Vite **5 a 10 vezes mais lentos**. Se a
pasta ainda estiver dentro do **OneDrive** ou Google Drive, piora: o serviço de
sincronização fica disputando os mesmos arquivos.

Rodando dentro do WSL2 o projeto fica num sistema de arquivos Linux de verdade, e
a lentidão simplesmente não existe.

### Instalando

Abra o **PowerShell como Administrador** (botão direito no menu Iniciar →
*Terminal (Admin)*) e rode:

```powershell
wsl --install
```

Isso instala o WSL2 e o Ubuntu de uma vez. **Reinicie o computador** quando ele pedir.

Se o WSL já existia na máquina e só falta a distro:

```powershell
wsl --install -d Ubuntu
wsl --set-default-version 2
```

Depois de reiniciar, o Ubuntu abre sozinho e pede para você criar um **usuário e
senha do Linux** (não precisa ser igual ao do Windows — e a senha não aparece na
tela enquanto você digita, isso é normal).

### Confirme que ficou na versão 2

```powershell
wsl --list --verbose
```

Precisa aparecer `VERSION` igual a **2**:

```
  NAME      STATE           VERSION
* Ubuntu    Running         2
```

Se aparecer `1`, converta:

```powershell
wsl --set-version Ubuntu 2
```

---

## 3. Passo 2 — Instalar o Docker Desktop

1. Baixe em <https://www.docker.com/products/docker-desktop/>
2. Rode o instalador e deixe **marcada** a opção *Use WSL 2 instead of Hyper-V*
3. Reinicie se for solicitado
4. Abra o Docker Desktop e espere a baleia no canto inferior esquerdo ficar
   **verde / "Engine running"**

> O Docker Desktop precisa estar **aberto** para os comandos funcionarem. Se você
> fechar a janela ele continua rodando na bandeja do sistema; se você sair de vez
> (*Quit*), nenhum comando `docker` funciona.

Para que ele suba sozinho com o Windows: *Settings → General → Start Docker Desktop
when you sign in*.

---

## 4. Passo 3 — Ligar a integração Docker ↔ WSL

Sem este passo o comando `docker` **não existe** dentro do Ubuntu.

1. Abra o **Docker Desktop**
2. Clique na engrenagem (**Settings**)
3. Vá em **Resources → WSL Integration**
4. Deixe ligado **Enable integration with my default WSL distro**
5. Ligue também a chavinha do **Ubuntu** na lista
6. **Apply & Restart**

### Confirme

Abra o **Ubuntu** (menu Iniciar → digite "Ubuntu") e rode:

```bash
docker --version
docker compose version
```

As duas precisam responder com um número de versão. Se der `command not found`,
volte e revise a integração acima.

---

## 5. Passo 4 — Baixar o projeto

**Tudo daqui para frente é dentro do terminal do Ubuntu**, não do PowerShell e não
do CMD.

```bash
mkdir -p ~/projetos
cd ~/projetos
git clone <url-do-repositorio> software-house
cd software-house
```

Confirme que você está no lugar certo:

```bash
pwd
```

Precisa mostrar algo como `/home/seu-usuario/projetos/software-house`.

> ### ⚠️ Nunca clone em `/mnt/c/...`
>
> Dentro do WSL, `/mnt/c/` é o disco do Windows visto de dentro do Linux. Clonar
> ali te devolve exatamente a lentidão que o WSL2 existe para resolver — e se for
> dentro do OneDrive, junta os dois problemas.
>
> - ✅ Certo: `/home/seu-usuario/projetos/software-house`
> - ❌ Errado: `/mnt/c/Users/voce/OneDrive/Documentos/software-house`
>
> O disco do WSL2 fica em `AppData\Local\Packages\...\ext4.vhdx`, que o OneDrive
> não sincroniza. Os dois convivem numa boa, desde que você não clone dentro da
> pasta sincronizada.

### Abrindo no VS Code

De dentro da pasta do projeto, no Ubuntu:

```bash
code .
```

O VS Code abre no Windows já conectado ao WSL (canto inferior esquerdo mostra
`WSL: Ubuntu`). Instale a extensão **WSL** da Microsoft se ele sugerir.

---

## 6. Passo 5 — Configurar o `.env`

O `.env` guarda as senhas e portas. Ele **não vai para o Git** — por isso o
repositório traz um modelo, o `.env.example`.

```bash
cp .env.example .env
```

Agora abra o `.env` e preencha as **duas senhas** que vêm vazias:

```env
DB_PASSWORD=
MYSQL_ROOT_PASSWORD=
```

Configure qualquer credencial adicional somente no `.env` local. Valores reais
nunca devem aparecer neste documento, em commits, issues ou mensagens de PR.

Gere senhas aleatórias sem sair do terminal:

```bash
echo "DB_PASSWORD=$(openssl rand -hex 16)"
echo "MYSQL_ROOT_PASSWORD=$(openssl rand -hex 16)"
```

Copie o resultado para dentro do `.env`.

> **Não use `$` nas senhas.** O Docker Compose interpreta `$` como início de
> variável e a senha chega truncada no container. `openssl rand -hex` já devolve
> só letras e números, então está seguro.

A `APP_KEY` fica vazia por enquanto — o `make setup` gera ela no próximo passo.

### Escolha o nome dos seus containers (opcional)

```env
CONTAINER_PREFIX=software-house
```

É o nome que você vai digitar todo dia (`docker exec -it software-house_app bash`).
Troque por `deploy`, `meuapp` ou o que preferir — detalhes na
[seção 9](#9-entrar-dentro-do-container).

### Portas ocupadas

Se você já tem outra coisa usando a porta 8000, mude **no `.env`**, nunca no
`compose.yaml`:

```env
APP_PORT=8001
VITE_PORT=5176
# Ferramentas administrativas: defina portas somente no `.env` local
```

---

## 7. Passo 6 — Subir o projeto

```bash
make setup
```

Esse único comando constrói as imagens, sobe os containers, instala as
dependências PHP e JS, gera a `APP_KEY`, roda as migrations, cria o link do
storage e compila o front-end.

**Na primeira vez demora de 5 a 15 minutos** (está baixando as imagens base).
Nas próximas, segundos.

### Dados de exemplo (opcional, mas recomendado na 1ª vez)

O `setup` deixa o banco **vazio**. Para popular a fatia técnica demonstrativa
atual e criar um usuário administrador:

```bash
make seed
```

Enquanto o scaffold atual ainda não for substituído pelos módulos da Deploy,
isso cria registros demonstrativos locais. As credenciais administrativas não
são documentadas nem versionadas: crie o usuário localmente, com senha exclusiva,
seguindo o procedimento interno da equipe.

O comando é seguro de repetir: rodar de novo não duplica nada.

### Se `make` não existir

O Ubuntu do WSL normalmente já traz. Se disser `command not found`:

```bash
sudo apt update && sudo apt install -y make
```

Ou rode os passos na mão, sem `make`:

```bash
docker compose build
docker compose up -d
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan migrate --seed
docker compose run --rm vite npm install
docker compose run --rm vite npm run build
```

> **No PowerShell do Windows** existe o equivalente `.\make.ps1 setup` — o
> Windows não traz `make`, e instalar só para isso contrariaria a regra de "só
> Docker, Git e um editor". Mas o caminho recomendado continua sendo o Ubuntu.

---

## 8. Passo 7 — Conferir se está tudo no ar

```bash
docker compose ps
```

Os 9 serviços precisam aparecer como `Up`, e os que têm healthcheck como
`(healthy)`:

```
NAME                  STATUS
software-house_app           Up (healthy)
software-house_mysql         Up (healthy)
software-house_nginx         Up (healthy)
software-house_queue         Up (healthy)
software-house_redis         Up (healthy)
software-house_scheduler     Up (healthy)
software-house_vite          Up
<ferramenta-admin>           Up
<captura-email-local>        Up (healthy)
```

### Abra no navegador

| Endereço | O que é |
|---|---|
| <http://localhost:8000> | **A aplicação** |
| API local | Consulte as rotas registradas no próprio ambiente de desenvolvimento |
| Ferramentas administrativas | Consulte a configuração local; endereços e portas não são publicados |

Para validar serviços internos ou rotas operacionais, siga o procedimento local
da equipe. Esses endereços não são mantidos na documentação pública.

---

## 9. Entrar dentro do container

Para rodar comandos por dentro (artisan, composer, olhar arquivos), você abre um
shell dentro do container.

### O nome do container é você quem escolhe

Antes do comando, entenda de onde vem o nome. No `.env` existe:

```env
CONTAINER_PREFIX=software-house
```

Esse valor é **uma sugestão, não uma regra**. Troque pelo que fizer sentido para
você — `deploy`, `meuapp`, qualquer coisa. Os containers passam a se chamar
`<prefixo>_app`, `<prefixo>_mysql`, e assim por diante:

| `CONTAINER_PREFIX` | Container da aplicação | Comando |
|---|---|---|
| `software-house` (sugerido) | `software-house_app` | `docker exec -it software-house_app bash` |
| `deploy` | `deploy_app` | `docker exec -it deploy_app bash` |
| `meuapp` | `meuapp_app` | `docker exec -it meuapp_app bash` |

Depois de mudar o prefixo, aplique com:

```bash
make down && make up
```

> **E se eu apagar a linha `CONTAINER_PREFIX`?** Nada quebra. O Compose cai no
> `COMPOSE_PROJECT_NAME` e, na falta dele, no nome da pasta — que é exatamente o
> comportamento padrão do Docker.

Os exemplos daqui para frente usam `software-house` porque é o valor que vem no
`.env.example`. **Troque pelo seu.**

### O jeito direto

```bash
docker exec -it software-house_app bash
```

- `docker exec` — executa um comando num container que **já está rodando**
- `-it` — modo interativo com terminal (é o que te dá o prompt)
- `software-house_app` — o **nome do container** (o seu prefixo + `_app`)
- `bash` — o shell que você quer abrir

Você cai em `/var/www/html`, que é a raiz do projeto dentro do container. Para
sair, digite `exit`.

Se preferir `sh`, também funciona:

```bash
docker exec -it software-house_app sh
```

> **Por que `bash` funciona aqui.** A imagem é Alpine Linux, que por padrão só tem
> o `sh`. O `bash` foi instalado de propósito **no stage `dev`** justamente porque
> `docker exec -it ... bash` é o comando que todo mundo digita por reflexo. Na
> imagem de produção ele não existe — lá a imagem é enxuta e se usa `sh`.

### Os nove containers

Trocando `software-house` pelo seu prefixo:

| Container | Serviço |
|---|---|
| `software-house_app` | PHP-FPM (a aplicação) |
| `software-house_nginx` | Servidor web |
| `software-house_mysql` | Banco de dados |
| `software-house_redis` | Cache / fila / sessão |
| `software-house_queue` | Worker das filas (Horizon) |
| `software-house_scheduler` | Agendador de tarefas |
| `software-house_vite` | Build do front-end |
| `<ferramenta-admin>` | Interface administrativa local, quando habilitada |
| `<captura-email-local>` | Captura local de e-mails, quando habilitada |

Para ver os nomes reais que estão no ar na sua máquina:

```bash
docker ps --format "table {{.Names}}\t{{.Status}}"
```

> ⚠️ **Nome de container é único por máquina.** Se você rodar **duas cópias** deste
> projeto ao mesmo tempo (duas pastas diferentes), a segunda falha com
> `container name is already in use`. É só dar um prefixo diferente à segunda.

### O jeito alternativo (não depende do prefixo)

```bash
docker compose exec -u www-data app bash
```

Aqui você usa o **nome do serviço** (`app`), que está no `compose.yaml` e é o
mesmo para todo mundo, independentemente do prefixo escolhido. Útil em
documentação e scripts compartilhados.

> **Por que `-u www-data`?** O `docker compose exec` não passa pelo *entrypoint*,
> que é quem rebaixa o usuário. Sem essa flag você entra como **root**, e todo
> arquivo que criar (um `make:controller`, por exemplo) nasce pertencendo ao root
> — depois você não consegue editar pelo VS Code sem `sudo`. O atalho pronto é:
>
> ```bash
> make shell
> ```

### Entrar em outros containers

```bash
docker exec -it software-house_mysql bash   # banco de dados
docker exec -it software-house_nginx sh     # nginx (Alpine puro, sem bash)
docker exec -it software-house_vite sh      # Node/Vite
```

### Rodar um comando sem abrir shell

```bash
docker compose exec -u www-data app php artisan migrate:status
docker compose exec -u www-data app php artisan route:list
```

---

## 10. Comandos do dia a dia

Com `make` (dentro do Ubuntu):

| Comando | O que faz |
|---|---|
| `make up` | Sobe os containers |
| `make down` | Para os containers (**dados preservados**) |
| `make ps` | Estado dos containers |
| `make logs-app` | Acompanha os logs em tempo real |
| `make shell` | Abre shell no container da aplicação |
| `make test` | Roda os testes |
| `make check` | Pipeline completo: formatação + análise + testes |
| `make migrate` | Aplica as migrations |
| `make artisan c="..."` | Qualquer comando artisan |
| `make composer c="..."` | Qualquer comando composer |
| `make npm c="..."` | Qualquer comando npm |
| `make hook-install` | Ativa os git hooks (uma vez por clone) |

Exemplos:

```bash
make artisan  c="make:controller RelatorioController"
make composer c="require spatie/laravel-permission"
make npm      c="install chart.js"
```

Ver a lista completa:

```bash
make help
```

### Instalar pacote novo — sempre por dentro do container

```bash
# ✅ certo
docker compose run --rm app  composer require vendor/pacote
docker compose run --rm vite npm install alguma-lib

composer require vendor/pacote   # ❌ errado — exigiria PHP instalado no host
npm install alguma-lib           # ❌ errado — exigiria Node instalado no host
```

---

## 11. Parar, religar e desinstalar

```bash
make down          # para os containers — banco e dados PRESERVADOS
make up            # religa de onde parou
```

Desligar o computador não apaga nada: os dados ficam em volumes do Docker.

Para apagar **tudo, inclusive o banco** (irreversível):

```bash
make destroy CONFIRM=yes
```

---

## 12. Troubleshooting

### `docker: command not found` dentro do Ubuntu

A integração WSL não está ligada. Volte ao [Passo 3](#4-passo-3--ligar-a-integração-docker--wsl).

### `Cannot connect to the Docker daemon`

O Docker Desktop não está aberto. Abra e espere a baleia ficar verde.

### `port is already allocated`

Outra coisa está usando a porta. Descubra quem, no PowerShell:

```powershell
netstat -ano | findstr :8000
```

Ou simplesmente mude a porta no `.env` (`APP_PORT=8001`) e rode `make up`.

### 502 Bad Gateway

Quer dizer que o nginx está no ar mas não conseguiu falar com o PHP. Veja o log:

```bash
docker compose logs app --tail 50
```

O caso clássico era o nginx guardar o IP antigo do container `app` depois de um
rebuild. **Isso já está corrigido** neste projeto: o nginx resolve o DNS a cada
request (`resolver 127.0.0.11` em `docker/nginx/conf.d/app.conf`). Se mesmo assim
acontecer, `make restart` resolve.

### A página abre com erro 500

Quase sempre é `APP_KEY` vazia:

```bash
make artisan c="key:generate"
make artisan c="config:clear"
```

### O health check indica indisponibilidade do banco

O MySQL não subiu. Veja o motivo:

```bash
docker compose logs mysql --tail 30
```

Causa mais comum: `$` na senha dentro do `.env`. Troque por uma senha só com
letras e números e rode `make destroy CONFIRM=yes && make setup`.

### O projeto está lento

Confirme que você **não** está em `/mnt/c/`:

```bash
pwd
```

Se aparecer `/mnt/c/...`, mova o projeto para `~/projetos/` e clone de novo.

### Mudei o código e a página não atualiza

```bash
make artisan c="optimize:clear"
```

Se for JS/CSS, confira se o container `vite` está de pé (`docker compose ps`).

### Os git hooks não rodam

```bash
make hook-install
```

### `git clone` reclama e o `git status` mostra tudo como apagado

Sintoma no Windows: o clone termina com *"You can inspect what was checked out
with 'git status'"* e depois todos os arquivos aparecem como deletados, mesmo
existindo no disco.

É o limite de **260 caracteres de caminho do Windows** (MAX_PATH). Alguns
arquivos do projeto têm nomes longos (as migrations, por exemplo) e estouram o
limite quando a pasta de destino já é profunda.

Soluções, em ordem de preferência:

1. **Clone dentro do WSL2** (`~/projetos/`) — o Linux não tem esse limite, e é o
   caminho recomendado deste guia de qualquer forma
2. Clone numa pasta rasa no Windows (`C:\dev\gv` em vez de
   `C:\Users\voce\Documents\Projetos\Faculdade\...`)
3. Habilite caminhos longos no Git:

```bash
git config --global core.longpaths true
```

---

## 13. Linux e macOS

Não existe passo de WSL — o sistema de arquivos já é nativo (Linux) ou o Docker
Desktop já lida bem com bind mount (macOS, via virtiofs).

**Linux:** instale o Docker Engine + plugin Compose pelo gerenciador de pacotes da
sua distro e siga direto do [Passo 4](#5-passo-4--baixar-o-projeto).

**macOS:** instale o Docker Desktop e siga direto do [Passo 4](#5-passo-4--baixar-o-projeto).

Em ambos, do Passo 4 em diante os comandos são idênticos.

---

## Próximo passo

Com o projeto no ar, leia a **[ARQUITETURA.md](ARQUITETURA.md)** para entender o
que é cada serviço, cada pacote instalado e o porquê de cada decisão.
