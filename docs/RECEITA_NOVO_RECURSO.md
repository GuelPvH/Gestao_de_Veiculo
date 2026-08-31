# Receita: adicionar um recurso novo do zero

Este é o caminho completo, na ordem, para criar uma entidade nova com tela e API
— seguindo os padrões que os testes de arquitetura cobram.

> **Contexto:** `Vehicle` pertence ao scaffold técnico anterior ao escopo da
> Deploy. As referências abaixo continuam válidas apenas como exemplo da
> estrutura existente; não significam que frota faça parte do produto-alvo.

**Não invente estrutura.** Enquanto os primeiros módulos da Deploy ainda não
existirem, o recurso `Vehicle` é o exemplo técnico disponível para copiar:
cada passo abaixo aponta o arquivo real que serve de referência.

O exemplo usado aqui é **motorista** (`Driver`), que o projeto ainda não tem.
Troque pelo seu recurso.

> Todo comando roda em container (ADR [0001](adr/0001-container-first.md)). Se
> algum passo pedir para instalar algo na sua máquina, o passo está errado.

---

## Passo 0 — Antes de escrever código

1. **Nomeie o conceito** e registre em [GLOSSARIO.md](GLOSSARIO.md): termo em
   português, identificador em inglês. Faça isso primeiro — depois de vinte
   arquivos escritos, renomear é caro.
2. **Liste os campos** e, para cada um, o tipo, se é obrigatório e se é único.
   Essa lista vira a migration e as regras de validação; decidi-la antes evita
   descobrir no meio que faltava um campo.
3. **Decida quem pode o quê.** Ler é público? Criar exige autenticação? Alguma
   operação é só de administrador? Isso vira a Policy.

## Passo 1 — Branch

```bash
git checkout master
git pull
git checkout -b feat/driver
```

Commit direto em `master` é recusado (hook local e branch protection).

## Passo 2 — Migration

```bash
make artisan c="make:migration create_drivers_table"
```

Nome em inglês, no padrão `create_<tabela>_table`. Referência:
`database/migrations/2026_08_15_120000_create_vehicles_table.php`.

- Coluna única no banco (`->unique()`), não só na validação. Validação é a
  mensagem bonita; a constraint é a garantia — duas requisições simultâneas
  passam pela validação e só o banco segura.
- Coluna de situação como `string`, não `enum` do MySQL: o cast para o enum do
  PHP acontece no Model, e `ENUM` de banco é doloroso de alterar depois.
- Chave estrangeira com `->constrained()` e o comportamento de exclusão
  explícito (`->cascadeOnDelete()` ou `->restrictOnDelete()`).

```bash
make migrate
```

## Passo 3 — Enum (se houver situação/tipo)

`app/Enums/DriverStatus.php`. Referência: `app/Enums/VehicleStatus.php`.

Enum **sempre** que o campo tiver um conjunto fechado de valores. Nunca string
solta: string aceita `"disponivell"` e o erro só aparece na tela.

- `enum DriverStatus: string`, com `case Active = 'ativo';`
- valor persistido em português (é dado), nome do `case` em inglês (é código);
- `label()` devolve o texto exibido; `badgeClass()`, a classe do badge, se a tela
  precisar.

## Passo 4 — Model

`app/Models/Driver.php`. Referência: `app/Models/Vehicle.php`.

- `final class`, `declare(strict_types=1)`;
- docblock `@property` para cada coluna — é isso que faz o PHPStan nível 8 e o
  autocomplete do editor entenderem o Model;
- `$fillable` com o que pode vir de formulário. **Nunca** coloque coluna que só o
  sistema define (`is_admin`, `owner_id`) em `$fillable`;
- `casts()` com o enum e os tipos (`'birth_date' => 'date'`);
- `HasFactory`.

## Passo 5 — Factory (e seeder, se fizer sentido)

`database/factories/DriverFactory.php`. Referência:
`database/factories/VehicleFactory.php`.

A factory **não é opcional**: sem ela, todo teste vira quinze linhas de setup e
as pessoas param de escrever teste.

- `definition()` devolve dados plausíveis (`fake()->name()`), não `'teste'`;
- crie *states* para os casos que os testes vão pedir: `->active()`,
  `->suspended()`;
- campo único usa `fake()->unique()`.

## Passo 6 — Action (a regra de negócio)

`app/Actions/ListDrivers.php`. Referência: `app/Actions/ListVehicles.php`.

Uma Action por operação. As três regras que os testes de arquitetura cobram:

- `final readonly class`;
- **um** método público, chamado `handle()`;
- **nenhuma facade** (`DB::`, `Cache::`, `Auth::`). Dependência entra pelo
  construtor — é o que torna a Action testável sem o framework inteiro.

Nome no imperativo: `ListDrivers`, `SuspendDriver`, `AssignVehicleToDriver`.
Se você está tentado a chamar de `DriverService`, releia o
ADR [0002](adr/0002-actions-em-vez-de-services-e-repositories.md).

> Operação trivial (listar tudo sem regra) pode ficar direto no controller. Se
> tem condição, cálculo ou mais de uma etapa, é Action.

## Passo 7 — Policy (quem pode)

`app/Policies/DriverPolicy.php`. Referência: `app/Policies/VehiclePolicy.php`.

O Laravel descobre pelo nome (`Driver` → `DriverPolicy`): não há registro a
fazer. `final readonly class`, um método por operação (`create`, `update`,
`delete`, e `viewAny`/`view` se a leitura não for pública).

Mesmo que hoje a resposta seja "qualquer pessoa autenticada", **escreva a
Policy**. É onde a regra vai ser mudada depois, e uma linha aqui vale por um
`if` em cada controller.

## Passo 8 — FormRequest (o que é válido)

`app/Http/Requests/StoreDriverRequest.php` e `UpdateDriverRequest.php`.
Referência: os dois arquivos equivalentes de `Vehicle`.

- `final class`, estende `FormRequest`;
- `authorize()` **só delega** para a Policy (`Gate::allows(...)`);
- `rules()`: `required` no store, `sometimes` no update;
- `Rule::enum(DriverStatus::class)` em vez de lista de strings à mão;
- `Rule::unique(...)->ignore($id)` no update, senão salvar sem mudar o campo
  falha contra o próprio registro;
- `attributes()` com os nomes em português, para a mensagem de erro.

Controller **nunca** chama `validate()`. Ver
ADR [0006](adr/0006-validacao-em-form-request-autorizacao-em-policy.md).

## Passo 9 — Resource (o que a API devolve)

`app/Http/Resources/DriverResource.php`. Referência:
`app/Http/Resources/VehicleResource.php`.

Controller de API **nunca** devolve Model:

```php
return DriverResource::collection($drivers);   // ✅
return Driver::all();                          // ❌ vaza toda coluna da tabela
```

Liste os campos um por um. É trabalho chato e é exatamente o que impede um
`password_hash` ou um `document` de vazar no dia em que a coluna for criada.

## Passo 10 — Controller

```bash
make artisan c="make:controller Api/DriverApiController --api"
make artisan c="make:controller DriverController"
```

Referência: `app/Http/Controllers/Api/VehicleApiController.php` (API) e
`app/Http/Controllers/VehicleController.php` (tela).

- `final class`, estende o `Controller` base do projeto;
- recebe a Action por injeção no método;
- **sem regra de negócio, sem validação, sem `if` de autorização**. Se o método
  tem mais de umas dez linhas, algo que é de outra camada está ali.

## Passo 11 — Rotas

`routes/api.php` e `routes/web.php`.

- API: `Route::apiResource('drivers', DriverApiController::class)`;
- **nomeie explicitamente** (`->names('api.drivers')`) para não colidir com a
  rota web de mesmo nome — `route:cache` recusa nomes duplicados, e isso quebra
  o build de produção, não a suíte;
- escrita (`store`/`update`/`destroy`) dentro do grupo `auth:sanctum`;
- confira depois: `make artisan c="route:list --path=drivers"`.

## Passo 12 — View (se houver tela)

`resources/views/drivers/index.blade.php`, dentro do layout de
`resources/views/layouts/app.blade.php`.

Blade **não** acessa Model direto: recebe do controller o que precisa. Texto
exibido em português; use `$status->label()` e `$status->badgeClass()`, nunca um
`match` na view.

## Passo 13 — Testes

Sem isto o PR não passa: a cobertura mínima de 80% é cobrada no CI.

**`tests/Unit/DriverStatusTest.php`** — enum: `label()`, `badgeClass()`, `tryFrom`
com valor inválido.

**`tests/Feature/DriverIndexTest.php`** — tela: status 200, o que aparece, o que
não aparece, filtro.

**`tests/Feature/Api/DriverApiTest.php`** — a API inteira. Copie a estrutura de
`tests/Feature/Api/VehicleApiTest.php`, que já cobre:

- lista e exibe;
- cria autenticado (`Sanctum::actingAs(...)`) → 201;
- **bloqueia sem autenticação** → 401;
- rejeita sem campos obrigatórios → 422;
- rejeita valor duplicado e valor fora do enum → 422;
- atualiza, remove;
- 404 em recurso inexistente;
- o Resource não expõe campo sensível.

Nome do teste em português, descrevendo comportamento: `it('rejeita placa já
cadastrada')`.

```bash
make test
make artisan c="test --filter=DriverApi"
```

## Passo 14 — Verificação completa

```bash
make check
```

Roda Pint → Rector (dry-run) → PHPStan → Pest, na mesma ordem do CI. Se passa
aqui, passa lá.

Formatação: `make lint-fix`.

## Passo 15 — Documentação e PR

- termo novo no [GLOSSARIO.md](GLOSSARIO.md) (você já fez no passo 0);
- variável de ambiente nova no `.env.example` (há teste que cobra);
- decisão estrutural nova → um [ADR](adr/README.md);
- entrada no [CHANGELOG.md](../CHANGELOG.md), em `Não lançado`;
- releia a [Definição de Pronto](DEFINICAO_DE_PRONTO.md);
- commit em Conventional Commits, PR com o template preenchido.

---

## Resumo: os arquivos de um recurso completo

```
database/migrations/AAAA_MM_DD_HHMMSS_create_drivers_table.php
database/factories/DriverFactory.php
app/Enums/DriverStatus.php
app/Models/Driver.php
app/Actions/ListDrivers.php
app/Policies/DriverPolicy.php
app/Http/Requests/StoreDriverRequest.php
app/Http/Requests/UpdateDriverRequest.php
app/Http/Resources/DriverResource.php
app/Http/Controllers/DriverController.php
app/Http/Controllers/Api/DriverApiController.php
resources/views/drivers/index.blade.php
routes/web.php            (editar)
routes/api.php            (editar)
tests/Unit/DriverStatusTest.php
tests/Feature/DriverIndexTest.php
tests/Feature/Api/DriverApiTest.php
docs/GLOSSARIO.md         (editar)
CHANGELOG.md              (editar)
```

Parece muito arquivo para uma entidade — e é a razão pela qual cada um deles é
pequeno e faz uma coisa só. Um recurso completo leva algumas horas na primeira
vez e bem menos na segunda, porque a lista acima é sempre a mesma.
