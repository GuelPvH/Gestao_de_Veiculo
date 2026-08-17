# 0004 — Suíte em SQLite na memória, com smoke test na stack real

- **Estado:** Aceita
- **Data:** 2026-08-17 (registro de decisão tomada no início do projeto)
- **Decide:** Miguel

## Contexto

A suíte precisa de banco. Três propriedades são desejáveis e conflitam entre si:

- **rapidez** — suíte lenta deixa de ser rodada, e suíte que não roda não existe;
- **isolamento** — teste não pode apagar o banco de desenvolvimento de ninguém;
- **fidelidade** — o banco do teste deveria se comportar como o de produção.

MySQL entrega fidelidade e custa rapidez. SQLite em memória entrega rapidez e
isolamento perfeitos, e **mente** em alguns pontos: tipo `ENUM`, `JSON`, busca
*fulltext*, particularidades de collation e de `ALTER TABLE`.

## Decisão

A suíte roda em **SQLite na memória** (`phpunit.xml`), e a fidelidade é coberta
por outro mecanismo, não pelo banco de teste:

- `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`; `RefreshDatabase` é aplicado
  por diretório em `tests/Pest.php`, nunca arquivo por arquivo.
- O CI tem um job **smoke** que sobe a stack completa (nginx, FPM, MySQL, Redis),
  roda as migrations no MySQL de verdade, executa o seeder duas vezes para provar
  idempotência e bate nas rotas.
- Existe um database `<DB_DATABASE>_testing` já criado pelo init do MySQL
  (`docker/mysql/init/01-testing-db.sh`). Quando um teste específico depender de
  recurso que o SQLite não tem, ele aponta para lá — a exceção é local, não
  global.

Em outras palavras: velocidade por padrão, fidelidade onde ela é necessária.

## Alternativas consideradas

- **MySQL para tudo.** Suíte muito mais lenta (cada `RefreshDatabase` vira
  transação em banco de verdade, sobre bind mount) e sujeita a estado
  compartilhado. O hook de pre-push roda a suíte inteira: alguns minutos ali
  significam gente usando `--no-verify`.
- **SQLite em arquivo.** Mais lento que memória e ainda deixa arquivo sujo entre
  execuções, com falha que aparece só na segunda rodada.
- **SQLite sem smoke test.** É a combinação perigosa: erro de configuração do
  nginx, migration que só falha no MySQL ou upstream quebrado passariam pela
  suíte inteira e chegariam na master. O smoke test é o que torna o SQLite
  aceitável.

## Consequências

**Ganhos:** a suíte completa roda em ~30 segundos, o que a torna parte do fluxo
de trabalho e não um ritual; nenhum teste toca o banco de desenvolvimento;
paralelização é trivial.

**Custos:**

- O SQLite mente. Migration que usa recurso específico do MySQL pode passar aqui
  e falhar lá — cabe ao job de smoke pegar.
- Existem dois caminhos de banco para entender (suíte e smoke), o que é mais
  conceito para quem está começando.
- Teste que precisa de MySQL exige configuração explícita e sai do padrão.

**Como sabemos que deu errado:** se o job de smoke começar a pegar falha de
migration com frequência, o SQLite deixou de ser aproximação suficiente e a
suíte (ou parte dela) deve migrar para o database `_testing`.
