# 🚗 Projeto de Gestão de Veículos (Faculdade)

Olá, desenvolvedor! Seja muito bem-vindo ao nosso projeto. 

Se você está chegando agora, preparei este guia com o máximo de detalhes possível. A ideia é que você consiga rodar a aplicação sem dores de cabeça, de forma clara e passo a passo.

Existem duas formas principais de rodar este projeto na sua máquina:
1. **O jeito moderno (via Docker/Sail)** - Fortemente recomendado, pois isola tudo e evita conflitos.
2. **O jeito clássico (Local)** - Instalando PHP, Composer e Node diretamente na sua máquina.

Vou explicar as duas formas abaixo. Siga a que fizer mais sentido para a sua máquina atual.

---

## 🛠️ Opção 1: Rodando via Docker (Recomendado)

Nessa abordagem, você não precisa sujar sua máquina com várias versões de PHP, Node ou bancos de dados. O Docker cuida de tudo criando "caixinhas" (containers) com exatamente aquilo que a gente precisa.

### Pré-requisitos
- **Docker Desktop** instalado. ([Baixe aqui](https://www.docker.com/products/docker-desktop/))
- (Para Windows) **WSL 2** configurado e integrado ao Docker.

### Passo a Passo

1. **Clone o repositório:**
   Abra o seu terminal (preferencialmente o WSL ou Git Bash) e rode:
   ```bash
   git clone <URL_DO_REPOSITORIO>
   cd trabalho_da_faculdade_de_gestao_de_veiculo
   ```

2. **Crie o arquivo de configuração (.env):**
   O Laravel usa um arquivo oculto para guardar senhas e configurações. Copie o de exemplo:
   ```bash
   cp .env.example .env
   ```

3. **Instale as dependências iniciais (O "pulo do gato"):**
   Como você não tem o PHP e Composer na máquina, vamos usar um container temporário só para baixar a pasta `vendor` (onde fica o núcleo do Laravel):
   ```bash
   docker run --rm -v "${PWD}:/app" composer install
   ```

4. **Suba a aplicação com o Sail:**
   O Sail é a ferramenta do Laravel para lidar com o Docker de forma muito fácil.
   ```bash
   ./vendor/bin/sail up -d
   ```
   *Dica: o `-d` faz os containers rodarem em segundo plano (detached), liberando o seu terminal para outros comandos.*

5. **Gere a chave de segurança:**
   Toda aplicação Laravel precisa de uma chave criptográfica única.
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. **Crie o Banco de Dados (Migrations):**
   Vamos criar as tabelas no banco de dados que está rodando lá dentro do Docker.
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

7. **Compile o visual (Bootstrap via Vite):**
   Precisamos gerar o CSS e JS final para que o site fique bonito.
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```
   *Se você for mexer no código visual e quiser que atualize sozinho na tela, deixe rodando `./vendor/bin/sail npm run dev` em outro terminal.*

8. **Tudo pronto!**
   Acesse no seu navegador: [http://localhost](http://localhost)

---

## 💻 Opção 2: O Jeito Clássico (Instalando PHP e Composer Localmente)

Se você não pode ou não quer usar Docker de jeito nenhum, sem problemas! Vamos configurar o ambiente direto na sua máquina (Windows, Linux ou Mac).

### 1. Instalando o PHP
O nosso projeto usa o Laravel 11, o que exige o **PHP 8.2 ou superior**.
- **Windows:** A forma mais fácil é instalar o [Laragon](https://laragon.org/) (fantástico para Windows) ou o [XAMPP](https://www.apachefriends.org/pt_br/index.html). Eles já trazem o PHP pronto para uso. Se quiser apenas o PHP puro, baixe os binários e coloque nas Variáveis de Ambiente.
- **Linux (Ubuntu):** 
  Abra o terminal e rode:
  ```bash
  sudo apt update
  sudo apt install php php-cli php-mbstring php-xml php-bcmath php-curl php-sqlite3
  ```
- **Mac:** Se você usa Homebrew, rode: `brew install php`

### 2. Instalando o Composer
O Composer é o gerenciador de pacotes do mundo PHP. É ele quem gerencia o Laravel e todas as bibliotecas.
- **Windows:** Baixe o instalador `.exe` direto no site oficial: [https://getcomposer.org/download/](https://getcomposer.org/download/) e faça a instalação clássica do Windows ("Next > Next > Finish").
- **Linux / Mac:** Execute no terminal:
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  ```
- *Para verificar se instalou certinho, abra um NOVO terminal e digite `composer -v`. Ele deve mostrar a logo do Composer.*

### 3. Instalando o Node.js e NPM
Precisamos do Node para compilar os nossos pacotes visuais (Bootstrap, CSS, JS).
- Baixe a versão "LTS" (Recomendada) no site oficial: [https://nodejs.org/](https://nodejs.org/) e instale.
- *Verifique no terminal com `node -v` e `npm -v`.*

### 4. Rodando o Projeto (Passo a Passo)

Agora que sua máquina já é uma máquina de desenvolvedor de verdade, vamos subir o projeto:

1. **Clone o repositório:**
   ```bash
   git clone <URL_DO_REPOSITORIO>
   cd trabalho_da_faculdade_de_gestao_de_veiculo
   ```

2. **Crie o arquivo de ambiente:**
   ```bash
   cp .env.example .env
   ```
   *Se você for usar um banco MySQL local (do XAMPP, por exemplo), abra este arquivo `.env` e ajuste os dados (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Por padrão, o Laravel 11 usa SQLite, então você pode até pular essa configuração e testar direto!*

3. **Instale os pacotes do PHP (Vendor):**
   ```bash
   composer install
   ```

4. **Instale os pacotes Front-end (Node_modules):**
   ```bash
   npm install
   ```

5. **Gere a chave da aplicação:**
   ```bash
   php artisan key:generate
   ```

6. **Rode as migrações (Criação de Tabelas):**
   ```bash
   php artisan migrate
   ```

7. **Compile os arquivos CSS e JS do Bootstrap:**
   ```bash
   npm run build
   ```
   *(Lembre-se: Para trabalhar alterando o visual, deixe um terminal rodando `npm run dev`)*

8. **Inicie o servidor local do Laravel:**
   ```bash
   php artisan serve
   ```

9. **Acesse!**
   O terminal vai te devolver um endereço, geralmente `http://127.0.0.1:8000`. É só clicar ou colar no navegador.

---

💡 **Dica de Ouro:** 
Sempre que você der `git pull` e baixar código novo que seus colegas fizeram, saiba que eles podem ter adicionado bibliotecas novas ou criado tabelas novas. Então, acostume-se a rodar sempre `composer install` e `php artisan migrate` (ou `./vendor/bin/sail ...`) para manter o seu ambiente 100% atualizado e evitar aqueles erros misteriosos!

Boa sorte no trabalho da faculdade! Qualquer dúvida, estamos por aqui.
