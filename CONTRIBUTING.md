# Como contribuir

Guia de trabalho para quem vai desenvolver neste projeto. Se você ainda não tem o
ambiente no ar, comece pela **[docs/INSTALACAO.md](docs/INSTALACAO.md)**.

---

## A regra que não se quebra: Container First

**Nada é instalado na sua máquina.** PHP, Composer, Node, npm, MySQL e Redis
existem só dentro dos containers.

```bash
# ✅ certo
docker compose run --rm app  composer require vendor/pacote
docker compose run --rm vite npm install alguma-lib

composer require vendor/pacote   # ❌ errado — exigiria PHP instalado no host
npm install alguma-lib           # ❌ errado — exigiria Node instalado no host
```

Existe uma verificação automatizada disso (`scripts/audit-container-first.sh`) e
ela roda no CI. Documentação que ensina comando de host **falha o pipeline**.

---

## O fluxo de trabalho

### 1. Crie uma branch

Commit direto em `master`, `main` ou `develop` é barrado pelos git hooks — e,
no servidor, pela **branch protection** do GitHub, que exige Pull Request e CI
verde. A diferença importa: o hook é atalho para você descobrir em um segundo, e
é ignorável com `--no-verify`; a proteção do servidor não é. Ver
[docs/adr/0005](docs/adr/0005-hooks-locais-e-branch-protection.md).

```bash
git checkout master
git pull
git checkout -b feat/nome-curto-da-tarefa
```

Prefixos usados: `feat/`, `fix/`, `docs/`, `refactor/`, `test/`, `chore/`.

### 2. Ative os hooks (uma vez por clone)

```bash
make hook-install
```

O `captainhook/hook-installer` reexecuta isso a cada `composer install`, então
normalmente você não precisa lembrar.

### 3. Desenvolva

Rode a verificação antes de abrir o PR:

```bash
make check
```

Isso roda, nesta ordem: **Pint → Rector (dry-run) → PHPStan → Pest**. É
exatamente o que o CI executa — se passa aqui, passa lá.

### 4. Commit

A mensagem segue [Conventional Commits](https://www.conventionalcommits.org/) e
o hook `commit-msg` valida:

```
tipo(escopo opcional): descrição em até 72 caracteres
```

Tipos aceitos: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`,
`chore`, `ci`, `build`, `revert`.

```bash
git commit -m "feat(frota): filtra veiculos por placa"
git commit -m "fix: corrige 502 apos recriar o container do app"
```

Corpo com mais detalhes é bem-vindo — a validação é só na primeira linha.

### 5. Push e Pull Request

```bash
git push -u origin feat/nome-curto-da-tarefa
```

O `pre-push` roda a suíte inteira antes de deixar o push sair. É mais lento que o
`pre-commit` de propósito: acontece menos vezes.

Abra o PR preenchendo o template. O CI precisa estar verde para o merge.

Antes de abrir, passe os olhos na **[Definição de
Pronto](docs/DEFINICAO_DE_PRONTO.md)** — é a lista do que "pronto" significa
aqui, e existe para o PR não voltar três vezes no review. O **título** do PR
segue Conventional Commits: no merge por squash é ele que fica na história, e há
um check de CI que valida.

Vai revisar o PR de outra pessoa (ou receber o review do seu)? **[Revisão de
código](docs/REVISAO_DE_CODIGO.md)** diz o que olhar, em que ordem, e como
escrever o comentário.

---

## O que os hooks verificam

| Momento | O que roda |
|---|---|
| **pre-commit** | Bloqueio de branch protegida; Pint e PHPStan **só se houver `.php` no stage**; arquivo acima de 5 MB; detecção de segredo; marcador de conflito de merge esquecido |
| **pre-push** | Bloqueio de branch protegida; `composer validate`; Pint; PHPStan; **suíte inteira** |
| **commit-msg** | Conventional Commits |
| **post-checkout / post-merge** | `composer install`, só se o `composer.lock` mudou |

### "O hook está me atrapalhando"

Se um hook barrou você, ele encontrou alguma coisa. Leia a mensagem — ela diz o
quê. Resista a `--no-verify`: o hook é local e ignorável, mas o CI e a branch
protection **não são**. O resultado de pular o hook é a mesma falha, mais tarde e
na frente do time.

Se o hook estiver **errado**, isso é um bug do hook. Abra uma issue e corrija a
regra em `captainhook.json`, não a contorne.

---

## Padrões de código

A arquitetura em camadas está descrita em
**[docs/ARQUITETURA.md](docs/ARQUITETURA.md)**. O resumo do que os *arch tests*
cobrem — e que portanto **quebra a suíte** se for violado:

- Controllers concretos são `final` e estendem o `Controller` base
- Actions são `final`, têm um único `handle()` e **não usam facade**
- Jobs são `final` e enfileiráveis
- Enums não carregam estado
- Models não aparecem fora de `App\Actions`, `App\Http`, `App\Providers`,
  `App\Policies`, `Database` e `Tests`
- Sem `dd()`, `dump()` ou `var_dump()` esquecidos
- **Sem `env()` fora de `config/`**

> O último merece destaque: `config:cache` roda no build de produção e **congela**
> o `env()`. Qualquer chamada fora de um arquivo de config passa a devolver `null`
> em produção — e o bug só aparece lá.

- FormRequests são `final` e estendem o `FormRequest` do framework
- Policies são `final readonly`

### Controller de API nunca devolve Model

Sempre via Resource:

```php
return VehicleResource::collection($vehicles);   // ✅
return Vehicle::all();                           // ❌ vaza toda coluna da tabela
```

### Controller não valida e não autoriza

Validação mora em **FormRequest** (`app/Http/Requests/`), autorização em
**Policy** (`app/Policies/`):

```php
public function store(StoreVehicleRequest $request): JsonResponse   // ✅
{
    $vehicle = Vehicle::create($request->validated());              // ✅ validated(), nunca all()
}

public function store(Request $request): JsonResponse               // ❌
{
    $request->validate([...]);   // ❌ as regras vão divergir entre store e update
}
```

Motivo completo no
[ADR 0006](docs/adr/0006-validacao-em-form-request-autorizacao-em-policy.md).

> **Criando um recurso do zero?** Não invente a estrutura: siga a
> **[docs/RECEITA_NOVO_RECURSO.md](docs/RECEITA_NOVO_RECURSO.md)**, que lista os
> 15 passos e o arquivo de referência de cada camada.

### Nomes

Identificador em inglês, texto para pessoa em português. Os termos do domínio
(veículo, frota, placa, situação) têm um nome só, definido em
**[docs/GLOSSARIO.md](docs/GLOSSARIO.md)** — consulte antes de batizar coisa nova,
e acrescente o termo novo ali no mesmo PR.

---

## Testes

```bash
make test             # suíte completa
make test-coverage    # com cobertura (mínimo 80%) — é o que o CI cobra
```

O piso de 80% **falha o CI**, não é sugestão. Ele é um chão para a cobertura não
cair um PR por vez sem nada reclamar — não uma meta a perseguir (a cobertura atual
está acima disso). E cobertura não mede teste bom: teste que executa a linha sem
verificar nada conta igual. Ver
[ADR 0007](docs/adr/0007-cobertura-minima-de-80-por-cento.md).

Rodar um arquivo ou filtrar:

```bash
make artisan c="test --filter=VehicleApi"
```

Os testes usam **SQLite em memória**, então são rápidos e não tocam o banco de
desenvolvimento. Se a sua feature depender de recurso específico do MySQL (coluna
JSON, fulltext, `ENUM`), o SQLite mente — nesse caso aponte o `phpunit.xml` para
o banco `gestao_veiculo_testing`, que já existe.

### O que precisa de teste

- Regra de negócio nova → teste na Action
- Endpoint novo → teste de feature cobrindo sucesso **e** falha de autorização
- Validação nova → um teste por regra que você não gostaria que fosse apagada sem
  ninguém perceber
- Correção de bug → um teste que falha antes do fix e passa depois

> Teste que não falha quando o código está errado não é teste. Quebre a linha de
> propósito, veja vermelho, desfaça — leva dez segundos e é a única forma de saber
> que o teste testa algo.

---

## Migrations

```bash
make artisan c="make:migration create_drivers_table"
make migrate
```

O nome da migration é em inglês, no padrão do framework
(`create_<tabela>_table`, `add_<coluna>_to_<tabela>_table`) — igual às que já
existem. Ver [GLOSSARIO.md](docs/GLOSSARIO.md).

**Nunca edite uma migration que já foi para a `master`** — outras pessoas já a
rodaram. Crie uma nova.

`make fresh CONFIRM=yes` apaga e recria as tabelas. Só em desenvolvimento, e ele
exige a confirmação escrita justamente para não acontecer por acidente.

---

## Dependências

```bash
docker compose run --rm app  composer require vendor/pacote
docker compose run --rm app  composer require --dev vendor/pacote
docker compose run --rm vite npm install alguma-lib
```

**Commite sempre o `composer.lock` e o `package-lock.json` junto.** É o que
garante que todo mundo — e o CI — instale exatamente as mesmas versões.

Ferramenta de qualidade ou debug vai em `--dev`, nunca em `require`: o que está
em `require` vai para a imagem de produção.

---

## Segredos

- O `.env` **nunca** vai para o Git (está no `.gitignore`)
- Adicionou variável nova? Adicione também no **`.env.example`**, com um valor de
  exemplo ou vazio — quem clonar depois precisa saber que ela existe
- Removeu ou renomeou a configuração que lia uma variável? **Apague a linha do
  `.env.example`.** Chave que ninguém lê é pior que chave ausente: quem preenche
  acredita ter configurado. Há um teste (`tests/Architecture/EnvExampleTest.php`)
  que barra chave morta e chave duplicada
- Credencial de sistema externo (Sentry DSN, chave AWS) **não se inventa**: deixe
  vazia e documente a pendência
- O hook `pre-commit` tem detecção de segredo, mas ela é **rede de segurança**,
  não substituto de cuidado

---

## Precisa de ajuda?

1. **[docs/INSTALACAO.md](docs/INSTALACAO.md)** — ambiente e troubleshooting
2. **[docs/RECEITA_NOVO_RECURSO.md](docs/RECEITA_NOVO_RECURSO.md)** — o passo a passo de um recurso completo
3. **[docs/ARQUITETURA.md](docs/ARQUITETURA.md)** — como o projeto é organizado
4. **[docs/adr/](docs/adr/README.md)** — **por quê** é assim. Antes de propor mudar um padrão, leia o ADR dele
5. **[docs/GLOSSARIO.md](docs/GLOSSARIO.md)** — o nome certo de cada coisa
6. Abra uma issue descrevendo o que tentou e o que aconteceu

E, principalmente: **pergunte**. Trinta minutos travado no mesmo ponto é o limite
saudável. Traga o que você quer que aconteça, a mensagem de erro completa e o que
já tentou — nesse formato a resposta chega em minutos. Ficar travado em silêncio
por um dia é a única coisa aqui que conta como erro.
