# Definição de Pronto

> Uma tarefa não termina quando o código funciona na sua máquina. Termina quando
> outra pessoa consegue usar, entender e alterar o que você fez.

Este documento existe porque "pronto" é ambíguo, e ambiguidade custa caro em duas
direções: PR que volta três vezes no review, e PR que é aprovado faltando coisa.

Use como **última leitura antes de abrir o PR**. O checklist do próprio PR
(`.github/PULL_REQUEST_TEMPLATE.md`) é o resumo operacional disto.

---

## 1. Funciona — e você viu funcionando

- [ ] Você executou o caminho feliz **na aplicação**, não só no teste.
- [ ] Você executou pelo menos um caminho de erro: campo vazio, valor inválido,
      pessoa sem permissão, registro que não existe.
- [ ] Nada de `dd()`, `dump()`, `var_dump()` ou `console.log` esquecido. (Os
      testes de arquitetura barram os três primeiros; o quarto é com você.)
- [ ] Nenhum código comentado "para o caso de precisar depois". O Git é esse
      lugar.

## 2. Está testado onde importa

- [ ] Regra de negócio nova → teste na Action.
- [ ] Endpoint novo → teste de feature cobrindo **sucesso e falha de
      autorização**.
- [ ] Correção de bug → um teste que **falhava antes** do fix. Se você não viu o
      teste falhar, você não sabe se ele testa o bug.
- [ ] Validação nova → um teste por regra que você não gostaria que fosse apagada
      sem aviso.
- [ ] `make check` passa localmente: Pint, Rector (dry-run), PHPStan, Pest.

> **Teste que não falha quando o código está errado não é teste.** Um jeito
> rápido de conferir: quebre a linha de propósito, rode, veja vermelho, desfaça.

## 3. Está legível para quem não escreveu

- [ ] Nome diz a intenção: `ListVehicles`, não `VehicleHandler`; `$availableOnly`,
      não `$flag`.
- [ ] Identificadores em inglês, texto para pessoa em português — ver
      [GLOSSARIO.md](GLOSSARIO.md).
- [ ] Comentário explica **por quê**, nunca o quê. Se o comentário descreve o que
      a linha faz, o problema é o nome da variável ou do método.
- [ ] Decisão não óbvia (por que este `if`, por que esta ordem, por que não o
      caminho mais direto) está registrada — em comentário curto ou, se for
      estrutural, em um [ADR](adr/README.md).
- [ ] O método cabe na tela. Se não cabe, provavelmente faz duas coisas.

## 4. Não deixou armadilha para o próximo

- [ ] Variável de ambiente nova está no `.env.example`, com valor de exemplo ou
      vazia (há teste que cobra isso).
- [ ] Nenhuma credencial real no diff. Credencial de sistema externo se deixa
      vazia e documentada, nunca se inventa.
- [ ] Nenhum `env()` fora de `config/` — em produção o `config:cache` congela o
      valor e a chamada devolve `null`. (Teste de arquitetura barra.)
- [ ] Migration nova em vez de edição de migration já mergeada.
- [ ] Se instalou dependência: `composer.lock` / `package-lock.json` no commit, e
      ferramenta de dev em `require-dev`, nunca em `require`.
- [ ] Se mudou comando, fluxo ou variável: documentação atualizada no mesmo PR.
      Documentação em PR separado é documentação que não vai acontecer.

## 5. O PR é revisável

- [ ] **Uma coisa por PR.** Refactor grande e feature nova não viajam juntos —
      quem revisa não consegue separar o que é mudança de comportamento do que é
      mudança de forma.
- [ ] Diff pequeno o suficiente para ser lido com atenção. Acima de ~400 linhas,
      considere dividir; se não for possível, diga no PR por quê.
- [ ] Título em Conventional Commits (é o que sobra na história após o squash, e
      há um check de CI para isso).
- [ ] A descrição responde **o que muda**, **por quê** e **como testar**. O "por
      quê" é a única parte que o revisor não consegue deduzir do diff.
- [ ] Formatação não polui o diff: rode `make lint-fix` antes.

## 6. Depois de aprovado

- [ ] CI verde nos três jobs (qualidade, smoke, build de produção). Vermelho não
      se remerge esperando sorte — se um job falha por instabilidade, isso é um
      bug do pipeline e vale uma issue.
- [ ] Se o PR exige migration ou variável nova, isso está marcado na seção
      **Impacto** do template — é o que quem faz o deploy vai ler.

---

## O que fazer quando você travar

Trinta minutos é um bom limite. Passou disso sem avançar, peça ajuda — trazendo:

1. o que você quer que aconteça;
2. o que está acontecendo (mensagem de erro completa, não resumida);
3. o que você já tentou.

Pedir ajuda com esses três itens não é sinal de inexperiência: é o formato que
faz a resposta chegar em minutos em vez de horas. Travar em silêncio por um dia
é a única coisa aqui que conta como erro.
