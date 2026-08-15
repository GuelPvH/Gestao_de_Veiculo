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

- [ ] `make check` passa localmente (Pint, Rector, PHPStan, Pest)
- [ ] Adicionei/atualizei testes para o que mudou
- [ ] Se corrige um bug: existe um teste que falhava antes e passa agora
- [ ] Variável nova de ambiente foi adicionada ao `.env.example`
- [ ] Nenhuma credencial real foi commitada
- [ ] Documentação atualizada, se o comportamento mudou

## Impacto

- [ ] Precisa rodar migration (`make migrate`)
- [ ] Precisa de variável de ambiente nova
- [ ] Muda contrato da API (quebra clientes existentes)
- [ ] Nada disso — mudança isolada
