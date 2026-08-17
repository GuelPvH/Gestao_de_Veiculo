# 0007 — Cobertura mínima de 80%, cobrada no CI

- **Estado:** Aceita
- **Data:** 2026-08-17
- **Decide:** Miguel

## Contexto

O projeto já tinha o alvo `make test-coverage` com `--min=80`, e o
`CONTRIBUTING.md` já dizia "mínimo 80%". Mas o CI rodava a suíte **sem
cobertura**. O número existia em dois documentos e não era verificado em lugar
nenhum.

Esse é o pior estado possível de uma regra: ela parece garantida para quem lê,
e não é. Cobertura cai um PR por vez, sempre por um motivo razoável, e ninguém
percebe até o dia em que ela está em 40%.

## Decisão

O job de qualidade do CI passa a rodar `pest --parallel --coverage --min=80`. PR
que derruba a cobertura abaixo de 80% **não fica verde**.

Duas coisas que este ADR afirma explicitamente, para evitar a leitura errada:

1. **80% é piso, não meta.** A cobertura atual é de ~90%. O piso serve para
   impedir queda, não para autorizar chegar a 80.
2. **Cobertura não mede qualidade de teste.** Um teste que executa a linha e não
   verifica nada conta igual a um teste bom. Por isso a cobertura é o **último**
   critério da lista de qualidade, atrás de PHPStan, dos testes de arquitetura e
   do que o `docs/REVISAO_DE_CODIGO.md` cobra. O que a métrica pega bem é o outro
   caso: código que **ninguém** exercitou.

A medição usa **PCOV**, já presente na imagem `dev`, e não Xdebug: PCOV custa
cerca de 10% do tempo de execução, contra 2–3x do Xdebug.

## Alternativas consideradas

- **Sem gate, só o número na documentação.** Era o estado anterior. Documenta uma
  intenção e verifica nada.
- **Exigir 100%.** Empurra para o teste cerimonial: gente testando getter e
  provider para levantar o número, e o teste que importa continua não existindo.
- **Gate por *patch coverage*** (só as linhas do PR). Melhor conceitualmente —
  premia quem testa o que escreveu — mas exige serviço externo (Codecov e
  similares) e conta de terceiros. Fica como possível ADR futuro.
- **Testes de mutação (`pest --mutate`).** Mede o que a cobertura não mede:
  se o teste realmente falharia com o código quebrado. É a evolução natural
  deste ADR, mas custa tempo de CI que não se justifica com o tamanho atual do
  código.

## Consequências

**Ganhos:** a promessa dos 80% passa a ser real; código novo sem teste nenhum não
passa; a queda de cobertura vira evento visível no PR, não erosão silenciosa.

**Custos:**

- O job de qualidade fica ~10% mais lento.
- Existe o incentivo perverso de escrever teste ruim para levantar o número. O
  antídoto é humano — o checklist de review pergunta se o teste falharia caso o
  código estivesse errado.
- Arquivo legitimamente difícil de testar (integração com serviço externo, como
  `Support/SentryBeforeSend`) puxa o número para baixo e pode forçar a discussão
  sobre exclusão de cobertura.

**Como sabemos que deu errado:** PRs adicionando teste sem asserção real, ou
pedido recorrente para baixar o limite. O primeiro é problema de review; o
segundo é sinal de que a suíte deixou de acompanhar o código.
