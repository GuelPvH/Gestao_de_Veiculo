# Decisões de arquitetura (ADR)

Um **ADR** (*Architecture Decision Record*) é um arquivo curto que registra uma
decisão técnica: o que foi decidido, o contexto em que a decisão fazia sentido e
o que ela custa.

## Por que isto existe

Código responde **o quê**. Não responde **por quê**.

Sem esse registro, toda decisão do projeto vira folclore: alguém "acha que é
assim por causa do Docker", ninguém tem certeza, e a decisão é ou desfeita por
engano ou mantida por medo. As duas coisas são caras.

O ADR resolve isso de forma assimétrica e barata: são cinco minutos para
escrever e economizam a discussão inteira depois. Ele também dá permissão para
mudar de ideia — uma decisão registrada pode ser **substituída** de forma
explícita, com o motivo à vista.

## Como ler

Comece pelo número mais baixo. Os primeiros ADRs explicam o formato do
ambiente; os seguintes, o formato do código.

| # | Decisão | Estado |
|---|---|---|
| [0001](0001-container-first.md) | Nada é instalado no host: tudo roda em container | Aceita |
| [0002](0002-actions-em-vez-de-services-e-repositories.md) | Regra de negócio em Actions, sem camada de service/repository | Aceita |
| [0003](0003-phpstan-nivel-8-sem-baseline.md) | PHPStan nível 8, sem baseline | Aceita |
| [0004](0004-sqlite-em-memoria-nos-testes.md) | Suíte em SQLite na memória, com smoke test no MySQL real | Aceita |
| [0005](0005-hooks-locais-e-branch-protection.md) | Hooks locais como atalho, branch protection como portão | Aceita |
| [0006](0006-validacao-em-form-request-autorizacao-em-policy.md) | Validação em FormRequest, autorização em Policy | Aceita |
| [0007](0007-cobertura-minima-de-80-por-cento.md) | Cobertura mínima de 80% cobrada no CI | Aceita |

## Como escrever um novo

1. Copie o [`0000-template.md`](0000-template.md).
2. Numere em sequência (o próximo número livre) e dê um nome em kebab-case que
   descreva a decisão, não o assunto: `0008-fila-em-redis-em-vez-de-sqs.md`.
3. Preencha. **Uma página no máximo** — ADR longo não é lido, e ADR não lido é
   pior que ADR inexistente.
4. Adicione a linha na tabela acima.
5. Abra o PR. Um ADR é um convite à discussão: é normal que ele mude durante o
   review, e é aí que ele está fazendo o trabalho.

## Quando escrever

Escreva quando a decisão for **difícil de reverter** ou **contrariar a
expectativa** de quem chega:

- escolha de biblioteca ou serviço que vai ficar (fila, storage, monitoramento);
- padrão que todo código novo vai seguir (Actions, FormRequest, camadas);
- restrição de processo com efeito técnico (proibir commit direto na master);
- limite deliberado ("não usamos repository", "não versionamos o `.env`").

**Não** escreva para nome de variável, layout de tela ou coisa que o próprio
código explica.

## Regras do formato

- Um ADR **nunca é editado para mudar a decisão**. Se a decisão caiu, crie um
  novo ADR que a substitua e marque o antigo como `Substituída por 00XX`. A
  história das decisões é a parte útil: apagá-la devolve o projeto ao folclore.
- Corrigir typo, link ou acrescentar consequência descoberta depois: pode
  editar.
- Estados possíveis: **Proposta**, **Aceita**, **Substituída por 00XX**,
  **Descontinuada**.
