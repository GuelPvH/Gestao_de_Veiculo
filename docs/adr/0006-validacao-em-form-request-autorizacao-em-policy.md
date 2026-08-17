# 0006 — Validação em FormRequest, autorização em Policy

- **Estado:** Aceita
- **Data:** 2026-08-17
- **Decide:** Miguel

## Contexto

O `VehicleApiController` validava a entrada com `$request->validate([...])`
dentro dos próprios métodos. Consequências já visíveis com um único recurso:

- As regras de `plate`, `brand`, `model`, `year` e `status` existiam **duas
  vezes** (em `store` e em `update`), com diferenças que ninguém tinha decidido.
- A lista de status estava escrita à mão (`'in:disponivel,em_uso,manutencao'`),
  duplicando o `VehicleStatus`. Um `case` novo no enum passaria a ser recusado
  pela API sem ninguém lembrar deste arquivo.
- Não havia autorização nenhuma além do middleware `auth:sanctum`: "quem pode
  criar/editar/apagar veículo" era, implicitamente, "qualquer pessoa
  autenticada" — uma regra real, não escrita em lugar algum.

Com um recurso isso é um incômodo. Com cinco, é o cenário clássico do endpoint
que alguém esqueceu de proteger.

## Decisão

**Validação de entrada mora em FormRequest.** `app/Http/Requests/`, uma classe
por operação que recebe corpo (`StoreVehicleRequest`, `UpdateVehicleRequest`).
Controller nunca chama `validate()`. O controller usa `$request->validated()` —
nunca `all()`.

**Autorização mora em Policy.** `app/Policies/`, descoberta por convenção de
nome. O `authorize()` do FormRequest apenas delega (`Gate::allows(...)`); onde
não há corpo para validar (`destroy`), o controller chama `Gate::authorize(...)`
explicitamente.

**A lista de valores válidos vem do enum**, via `Rule::enum(VehicleStatus::class)`.

Duas regras de arquitetura foram adicionadas a `tests/Architecture/ArchTest.php`:
FormRequests são `final` e estendem o `FormRequest` do framework; Policies são
`final readonly` — Policy com estado interno mente sobre a decisão que tomou.

A Policy atual devolve `true` para pessoa autenticada, o que **preserva
exatamente o comportamento anterior**. O valor não está em restringir hoje: está
em existir um único arquivo onde a regra passa a ser escrita quando o primeiro
papel aparecer.

## Alternativas consideradas

- **Manter `validate()` no controller e extrair só quando doer.** "Quando doer" é
  quando as regras já divergiram e algum endpoint já está desprotegido. O custo
  de arrumar depois é maior, e um time júnior copia o padrão que encontra — o
  primeiro endpoint define o formato de todos os outros.
- **Regras compartilhadas em um método estático no Model.** Resolve a duplicação
  e não resolve a autorização, que é o problema mais grave dos dois. Também
  esconde da assinatura do controller o que ele exige.
- **Gate por rota (`->middleware('can:...')`).** Funciona, mas espalha a
  autorização entre `routes/api.php` e as classes. Com o FormRequest, quem lê o
  método do controller vê validação e autorização no mesmo lugar.

## Consequências

**Ganhos:** as regras de cada operação existem em um arquivo só; autorização tem
endereço; um `case` novo no enum é aceito automaticamente; mensagem de erro em
português via `attributes()`; controller volta a ter uma responsabilidade.

**Custos:**

- Dois arquivos a mais por recurso. Para quem está começando, é indireção: a
  regra que "estava ali" agora está em outro arquivo.
- A autorização acontece antes da validação, o que às vezes surpreende — quem não
  pode criar recebe 403 sem nunca saber se o corpo era válido. É o comportamento
  correto, e é uma pergunta que já apareceu.
- Policy que devolve `true` parece código inútil para quem não leu este ADR. O
  comentário no topo de `VehiclePolicy` existe por isso.

**Como sabemos que deu errado:** `$request->validate(` reaparecendo em qualquer
controller, ou um `if ($user->...)` de autorização dentro de controller ou
Action. Os dois indicam que o padrão não foi entendido, não que ele falhou.
