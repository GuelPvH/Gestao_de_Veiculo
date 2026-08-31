# Glossário e convenção de idioma

Duas coisas que parecem burocracia e economizam horas de review: **um nome só
para cada conceito** e **uma regra clara de quando escrever em inglês ou em
português**.

Sem isso, o mesmo conceito pode aparecer como `Orcamento`, `Budget`, `Proposal`
e `Quote` em arquivos diferentes — e ninguém consegue mais buscar nada no
projeto.

---

## 1. Regra de idioma

| O que | Idioma | Por quê |
|---|---|---|
| Nome de classe, método, variável, tabela, coluna | **inglês** | É o idioma do framework e das bibliotecas. Misturar produz `BuscaLeadRepository::getOrcamentos()` chamando `Proposal::all()` na linha seguinte. |
| Nome de rota e caminho de URL | **inglês** | Exemplos planejados: `/api/leads`, `projects.index`. Consistente com o restante do código. |
| Valor de enum persistido no banco | **inglês** | Mantém código, banco e integrações no mesmo vocabulário. A interface traduz o valor para português. |
| Comentário e docblock | **português** | O time todo lê português; o custo de escrever inglês mediano é uma explicação pior. |
| Mensagem exibida à pessoa (API, tela, e-mail) | **português** | É produto. Exemplo: `'Proposta enviada com sucesso.'` |
| Mensagem de commit, PR, issue, documentação | **português** | Mesmo motivo. O *tipo* do commit segue Conventional Commits e fica em inglês (`feat`, `fix`). |
| Nome de teste | **português** | `it('converte um lead fechado em projeto')` descreve comportamento para quem lê o relatório. |

**Regra de bolso:** se um computador lê, inglês. Se uma pessoa lê, português.

Nunca misture os dois em um mesmo identificador: `getOrcamento()`,
`ProjetoController` e `$listaDeLeads` estão todos errados.

---

## 2. Termos do domínio

Estes são os termos funcionais do produto-alvo descrito em
[ESCOPO.md](ESCOPO.md). Os identificadores técnicos são propostas de convenção e
só passam a representar implementação existente quando o respectivo módulo for
desenvolvido.

| Conceito (pt) | No código (en) | O que é |
|---|---|---|
| Lead | `Lead` | Contato comercial que demonstrou interesse nos serviços da Deploy. |
| Proposta | `Proposal` | Oferta comercial enviada a um lead, com escopo, prazo e valor. Evite `Budget`, que representa orçamento financeiro, não proposta comercial. |
| Cliente | `Client` | Pessoa ou empresa com relacionamento comercial confirmado com a Deploy. |
| Projeto | `Project` | Trabalho contratado e acompanhado por situação, prazo, valor e progresso. |
| Serviço | `Service` | Oferta da Deploy exibida no site, como sistema web, software sob medida ou API e integração. |
| Portfólio | `PortfolioProject` | Caso ou projeto selecionado para apresentação pública. Não confundir com o projeto operacional interno. |
| Atividade | `Activity` | Evento registrado no histórico de um lead ou projeto. |
| Transação | `Transaction` | Lançamento financeiro classificado como receita ou despesa. |
| Receita | `Revenue` | Entrada financeira vinculada ou não a um projeto. |
| Despesa | `Expense` | Saída financeira categorizada pela empresa. |
| Conteúdo do site | `SiteContent` | Textos, serviços, portfólio e perguntas frequentes administrados pela plataforma. |
| Solicitação de acesso | `AccessRequest` | Pedido de criação ou liberação de acesso à plataforma administrativa. |

---

## 3. Nomes de estrutura (as convenções que o projeto cobra)

Estas não são preferência: os testes de arquitetura quebram se forem violadas.

| Camada | Padrão de nome | Exemplo |
|---|---|---|
| Action | verbo + substantivo, imperativo | `ListLeads`, `ConvertLeadToProject` |
| Controller | recurso + `Controller` | `LeadController`, `ProjectController` |
| FormRequest | operação + recurso + `Request` | `StoreLeadRequest`, `UpdateProjectRequest` |
| Policy | recurso + `Policy` | `LeadPolicy`, `ProjectPolicy` |
| Resource | recurso + `Resource` | `LeadResource`, `ProjectResource` |
| Job | verbo + substantivo | `SendProposal`, `PublishSiteContent` |
| Enum | recurso + atributo | `LeadStatus`, `ProjectStatus` |
| Migration | ação + tabela, em inglês | `create_leads_table`, `create_projects_table` |
| Teste de feature | recurso + assunto + `Test` | `LeadConversionTest`, `ProjectStatusTest` |

Nomes proibidos, porque não dizem nada: `Manager`, `Handler`, `Helper`,
`Util(s)`, `Service` genérico, `data`, `info`, `temp`, `aux`, `x`.

---

## 4. Ao adicionar um termo novo

Conceito de domínio novo **entra nesta tabela no mesmo PR** que o cria. É trinta
segundos de trabalho e evita, por exemplo, que "proposta" seja `Proposal` em um
arquivo e `Quote` em outro.

Se o termo em inglês não for óbvio, decida no PR — e a decisão está tomada para
sempre a partir dali.
