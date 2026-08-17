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
