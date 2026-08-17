# 0001 — Nada é instalado no host: todo comando roda em container

- **Estado:** Aceita
- **Data:** 2026-08-17 (registro de decisão tomada no início do projeto)
- **Decide:** Miguel

## Contexto

O projeto é acadêmico e colaborativo: as pessoas que vão mexer nele estão em
máquinas diferentes, com sistemas diferentes, e uma parte delas nunca configurou
um ambiente PHP. O caminho tradicional — cada um instala PHP, Composer, Node,
MySQL e Redis na própria máquina — produz três problemas previsíveis:

1. **"Na minha máquina funciona".** Versões divergentes de PHP ou de extensão
   fazem o mesmo código passar aqui e falhar lá. O tempo gasto nisso é maior que
   o tempo do trabalho em si.
2. **Onboarding longo.** Um dia inteiro instalando ferramenta é um dia sem
   escrever código, e é onde a maioria desiste.
3. **Divergência com produção.** A aplicação roda em container em produção; um
   ambiente local diferente esconde justamente os erros de configuração.

## Decisão

PHP, Composer, Node, npm, MySQL e Redis existem **somente dentro de
containers**. No host, só Docker, Docker Compose, Git e um editor.

- Todo comando é invocado via `docker compose run --rm <serviço> <comando>`, ou
  pelo atalho equivalente no `Makefile` / `make.ps1`.
- A documentação **não pode** ensinar comando de host. Isso é verificado por
  `scripts/audit-container-first.sh`, que roda no CI: doc que ensina comando de
  host **falha o pipeline**.
- As versões de PHP e Node são fixadas no `.env.example` e usadas no build.

## Alternativas consideradas

- **Instalação nativa com documentação boa.** Documentação não impede
  divergência de versão; só a descreve. E ninguém percebe que divergiu até um
  bug estranho aparecer.
- **Container só para banco e Redis, host para PHP/Node.** É o meio-caminho mais
  comum e o pior dos dois: ainda exige instalar PHP e Node no host (o passo
  difícil), e ainda deixa a versão de PHP divergir.
- **Verificar versão em runtime e avisar.** Trata o sintoma. O ambiente continua
  sendo responsabilidade de cada pessoa.

## Consequências

**Ganhos:** ambiente idêntico para todo mundo e igual ao do CI; onboarding é
clonar e rodar `make setup`; nenhuma ferramenta suja a máquina de ninguém.

**Custos:**

- Todo comando fica mais longo. O `Makefile` reduz isso, não elimina.
- Depende de Docker funcionando, o que em Windows significa WSL2 configurado.
- I/O em bind mount é mais lento — sensível em pasta sincronizada (OneDrive,
  Dropbox). Por isso o cache do PHPStan e do Rector fica dentro do container,
  fora do bind mount.
- Quem usa recurso do editor que exige o binário local (formatar ao salvar, ir
  para a definição em código do vendor) precisa abrir o editor dentro do
  container. Ver `.vscode/settings.json`.

**Como sabemos que deu errado:** se o time começar a instalar PHP no host "só
para o editor parar de reclamar", a decisão deixou de ser cumprida — e o valor
dela é zero se cumprida por metade. O sinal aparece no PR: comando de host na
documentação, ou `vendor/` com timestamp de host.
