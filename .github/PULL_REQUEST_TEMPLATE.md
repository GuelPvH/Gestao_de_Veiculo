## O que muda

<!-- Descreva em uma ou duas frases. Se fecha uma issue, escreva "Closes #123". -->

## Por quê

<!-- O problema que isso resolve. Se a motivação não for óbvia pelo diff, ela
     precisa estar aqui — é o que o revisor não consegue deduzir do código. -->

## Como testar

<!-- Passo a passo para o revisor reproduzir. Ex.:
     1. make up
     2. Acesse http://localhost:8000/veiculos
     3. Filtre por "em manutenção" e confirme que ... -->

## Checklist

<!-- A versão longa disto, com o porquê de cada item, está em
     docs/DEFINICAO_DE_PRONTO.md. Se for o seu primeiro PR aqui, leia lá. -->

- [ ] `make check` passa localmente (Pint, Rector, PHPStan, Pest)
- [ ] `make test-coverage` passa — o CI reprova abaixo de 80%
- [ ] Adicionei/atualizei testes para o que mudou
- [ ] Se corrige um bug: existe um teste que falhava antes e passa agora
- [ ] O teste falharia se o código estivesse errado (eu conferi quebrando de propósito)
- [ ] Variável nova de ambiente foi adicionada ao `.env.example` — e chave que
      deixou de ser lida foi removida dele
- [ ] Nenhuma credencial real foi commitada
- [ ] Termo novo do domínio registrado em `docs/GLOSSARIO.md`
- [ ] Decisão estrutural nova registrada em `docs/adr/`
- [ ] Entrada no `CHANGELOG.md`, em `Não lançado`
- [ ] Documentação atualizada, se o comportamento mudou
- [ ] **Uma coisa por PR**: refactor grande não viaja junto com feature nova

## Impacto

- [ ] Precisa rodar migration (`make migrate`)
- [ ] Precisa de variável de ambiente nova
- [ ] Muda contrato da API (quebra clientes existentes)
- [ ] Nada disso — mudança isolada
