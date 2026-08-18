# Changelog

Mudanças relevantes deste projeto, na ordem inversa (mais recente no topo).

O formato segue [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o
versionamento segue [SemVer](https://semver.org/lang/pt-BR/).

## Como escrever aqui

Este arquivo **não é o `git log`**. O log responde "quais commits existem"; o
changelog responde "o que mudou para quem usa e para quem mantém o projeto". Um
existe para a máquina, o outro para pessoas.

- Toda mudança visível entra em **`[Não lançado]`** no mesmo PR que a produz.
  Deixar para depois é o mesmo que não fazer.
- Escreva o **efeito**, não o diff: "a API recusa placa duplicada com 422", não
  "adiciona `Rule::unique` em `StoreVehicleRequest`".
- Uma linha por mudança, em português, no presente.
- Categorias: **Adicionado**, **Alterado**, **Corrigido**, **Removido**,
  **Depreciado**, **Segurança**.
- Refactor interno que não muda nada para ninguém **não** precisa de linha.
- Quebra de compatibilidade da API vai em **Alterado**, começando com
  `**QUEBRA:**` — é o que alguém procura antes de atualizar.

Ao lançar uma versão, renomeie `[Não lançado]` para `[X.Y.Z] - AAAA-MM-DD` e
abra uma nova seção `[Não lançado]` vazia.

---

## [Não lançado]

### Adicionado

- Validação da API de veículos em FormRequest (`StoreVehicleRequest`,
  `UpdateVehicleRequest`): as regras de criação e de atualização deixam de existir
  em duas cópias dentro do controller.
- Autorização de veículo em `VehiclePolicy`. O comportamento é o mesmo de antes
  (pessoa autenticada pode criar, editar e remover), mas a regra passa a ter um
  único lugar onde é escrita.
- Cobertura mínima de 80% cobrada no CI. O número já existia no `Makefile` e no
  `CONTRIBUTING.md`, mas nada o verificava.
- Check de CI que valida o título do PR em Conventional Commits — no merge por
  squash é o título que fica na história, e nenhum hook local o vê.
- `.github/CODEOWNERS`: review automática por área, com os caminhos sensíveis
  (`docker/`, `config/`, migrations, `app/Policies/`, `.github/`) exigindo o olho
  de quem responde por eles.
- `scripts/branch-protection.sh`: a proteção da branch principal como código
  versionado, em vez de configuração invisível na interface do GitHub.
- Registro de decisões de arquitetura em [`docs/adr/`](docs/adr/README.md), com
  template e as sete decisões que sustentam o projeto hoje.
- [`docs/DEFINICAO_DE_PRONTO.md`](docs/DEFINICAO_DE_PRONTO.md) — o que "pronto"
  significa antes de abrir PR.
- [`docs/REVISAO_DE_CODIGO.md`](docs/REVISAO_DE_CODIGO.md) — o que revisar, em que
  ordem, e como escrever o comentário.
- [`docs/GLOSSARIO.md`](docs/GLOSSARIO.md) — termos do domínio e a regra de
  idioma (identificador em inglês, texto para pessoa em português).
- [`docs/RECEITA_NOVO_RECURSO.md`](docs/RECEITA_NOVO_RECURSO.md) — os 15 passos
  para criar um recurso completo, com o arquivo de referência de cada camada.
- Configuração de editor compartilhada em `.vscode/` (fim de linha, exclusões de
  busca, extensões recomendadas).
- Testes: validação e autorização da API de veículos (422 em campo obrigatório,
  placa duplicada, status fora do enum, ano fora da faixa; 404 em recurso
  inexistente).
- Teste de arquitetura que trata o `.env.example` como contrato: recusa chave
  morta (que ninguém mais lê) e chave duplicada.
- Regras de arquitetura para as camadas novas: FormRequests `final` estendendo o
  `FormRequest` do framework; Policies `final readonly`.
- Este arquivo.

### Alterado

- A lista de situações aceitas pela API passa a vir do enum `VehicleStatus`
  (`Rule::enum`) em vez de uma lista de strings escrita à mão. Um `case` novo no
  enum passa a ser aceito sem alteração na validação.
- Mensagens de erro de validação da API usam os nomes dos campos em português.

### Removido

- `MAIL_ENCRYPTION` do `.env.example`: o Laravel 11 substituiu a chave por
  `MAIL_SCHEME` e a antiga não era lida por nada. Chave morta é pior que chave
  ausente — quem preenche acredita ter configurado.

### Corrigido

- A suíte de testes deixa de depender do build do front-end. O CI quebrava com
  `ViteManifestNotFoundException` em todo teste que renderiza uma página, porque
  `public/build/manifest.json` só é gerado na etapa que roda **depois** dos
  testes. O Vite passa a ser desligado na suíte; o manifest continua verificado
  onde importa — no smoke test, com os assets reais, e na imagem de produção.

---

> As mudanças anteriores a este arquivo estão no histórico do Git. O changelog
> começa aqui, não retroativamente: reconstruir changelog do passado é trabalho
> de arqueologia, e ninguém o lê.
