# 0002 — Regra de negócio em Actions, sem camada de service ou repository

- **Estado:** Aceita
- **Data:** 2026-08-17 (registro de decisão tomada no início do projeto)
- **Decide:** Miguel

## Contexto

Regra de negócio precisa morar em algum lugar que não seja o controller. A
pergunta é qual lugar.

O padrão que o ecossistema PHP herdou é a dupla *Service* + *Repository*: um
`VehicleService` com N métodos e um `VehicleRepository` embrulhando o Eloquent.
Em um projeto deste tamanho isso produz duas patologias concretas:

- **Service que só cresce.** `VehicleService` acumula `list`, `create`,
  `update`, `export`, `summarise`... Vira o mesmo controller gordo de antes, um
  arquivo adiante. Ninguém consegue dizer o que a classe faz, porque ela faz
  tudo.
- **Repository que embrulha o Eloquent.** O Eloquent JÁ é a camada de acesso a
  dados. Envolvê-lo em uma interface própria custa dois arquivos por entidade e
  entrega abstração que ninguém vai trocar — a promessa de "trocar o banco
  depois" nunca é exercida, e o Eloquent vaza pela interface de qualquer jeito.

## Decisão

Cada operação de negócio é uma **Action**: uma classe `final readonly` em
`app/Actions`, com **um único método público `handle()`**.

- O nome da classe é a operação: `ListVehicles`, `SummariseFleet`.
- O acesso a dados é Eloquent direto, sem repository.
- Actions **não usam facade**. Recebem dependência pelo construtor, o que as
  torna testáveis sem subir o framework.
- Controllers só orquestram: recebem a Action por injeção, chamam `handle()`,
  devolvem resposta.

As três regras acima são verificadas em `tests/Architecture/ArchTest.php` — não
são convenção, são teste que quebra.

## Alternativas consideradas

- **Service + Repository.** Descrito acima: mais arquivos, menos clareza, e a
  abstração que justifica o custo nunca é usada.
- **Regra no controller.** Funciona até o segundo lugar que precisa da mesma
  regra. Aí ela é copiada, e as duas cópias divergem.
- **Regra no Model.** Model com regra de negócio cresce sem limite e amarra a
  operação a uma entidade só — a maioria das operações reais atravessa mais de
  uma.

## Consequências

**Ganhos:** cada classe faz uma coisa e o nome diz qual; "onde está a regra X" é
respondido pelo nome do arquivo; a proibição de facade deixa a Action testável
isoladamente; o limite de um método impede a classe-canivete.

**Custos:**

- Muitas classes pequenas. Quem espera um service por entidade acha a pasta
  poluída.
- Uma operação que precisa de duas Actions exige compô-las explicitamente — mais
  verboso que chamar dois métodos do mesmo service.
- Sem repository, teste de Action toca o banco (SQLite em memória, ver ADR 0004).

**Como sabemos que deu errado:** se aparecer Action com `handle()` de 200 linhas
ou com nome genérico (`ManageVehicles`, `VehicleHandler`), a decisão está sendo
cumprida na forma e violada no conteúdo. Uma Action deve caber na tela.
