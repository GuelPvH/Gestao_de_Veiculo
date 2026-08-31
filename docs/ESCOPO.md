# Escopo do produto — Deploy

## 1. Visão do produto

A **Deploy** é uma software house que transforma ideias em soluções digitais e
desenvolve sistemas personalizados, plataformas web e aplicações escaláveis.

Este projeto tem como objetivo centralizar a operação comercial, operacional e
financeira da empresa em uma plataforma de gestão, além de manter o conteúdo do
site institucional.

O escopo foi definido a partir do arquivo de design privado aprovado pela equipe
da Deploy. O endereço do arquivo não é versionado neste repositório público.

> **Estado atual:** este documento descreve o produto-alvo. As telas e regras do
> Figma ainda não devem ser consideradas implementadas somente por estarem
> documentadas. O repositório conserva uma fatia técnica demonstrativa que será
> substituída gradualmente durante a construção do produto.

## 2. Públicos

- **Visitante:** conhece a Deploy, seus serviços e projetos e pode iniciar um
  contato ou solicitar orçamento.
- **Lead ou cliente potencial:** fornece as informações iniciais do projeto e
  acompanha o contato comercial pelos canais definidos pela empresa.
- **Equipe administrativa:** acompanha leads, propostas, projetos, conteúdo,
  indicadores financeiros e configurações da plataforma.

## 3. Site institucional

O ambiente público comunica a proposta de valor da Deploy e conduz visitantes
até o contato comercial.

### 3.1 Início

- apresentação da Deploy como empresa de desenvolvimento de software e soluções
  digitais;
- chamada para solicitar orçamento;
- acesso aos serviços e projetos;
- destaque para sistemas personalizados, plataformas web e aplicações
  escaláveis.

### 3.2 Serviços

O portfólio de serviços previsto no design inclui:

- desenvolvimento de sites;
- sistemas web de gestão;
- software sob medida;
- landing pages de alta conversão;
- dashboards e Business Intelligence;
- APIs e integrações;
- manutenção e evolução de software.

### 3.3 Projetos

- apresentação de projetos e casos da empresa;
- demonstração das soluções entregues e das tecnologias relacionadas;
- reforço da capacidade técnica e dos resultados da Deploy.

### 3.4 Contato e orçamento

- formulário de contato comercial;
- solicitação de orçamento;
- coleta das informações iniciais necessárias para transformar o contato em
  lead.

## 4. Plataforma administrativa

O painel administrativo é destinado à gestão interna da software house.

### 4.1 Autenticação e acesso

- login na conta Deploy;
- opção de manter a sessão conectada;
- recuperação de senha;
- solicitação de acesso para quem ainda não possui conta;
- encerramento de sessões e configuração de autenticação em dois fatores.

### 4.2 Dashboard

- quantidade de leads no período;
- projetos ativos;
- receita total;
- taxa de conversão de leads em projetos;
- evolução mensal dos leads;
- distribuição dos projetos por situação;
- atividades recentes;
- visão resumida dos últimos leads.

### 4.3 Leads e orçamentos

- cadastro e listagem de leads;
- busca e filtros por situação, tipo de projeto e período;
- indicadores de leads novos, em análise, com proposta e convertidos;
- registro de contato, empresa, tipo de projeto, prazo, objetivo e orçamento;
- histórico de atividades do lead;
- envio de proposta;
- agendamento de reunião;
- conversão do lead em projeto ou registro como perdido;
- exportação da listagem em CSV.

Situações de referência apresentadas no Figma: **novo**, **em análise**,
**proposta enviada**, **fechado** e **perdido**.

### 4.4 Projetos

- criação e acompanhamento de projetos;
- visualizações em Kanban e lista;
- filtros por período;
- organização por situação;
- cliente, responsável, tipo do projeto, datas, prazo e valor;
- tecnologias associadas;
- progresso percentual;
- visão de cronograma em linha do tempo ou Gantt.

Situações de referência apresentadas no Figma: **em análise**, **em andamento**,
**em revisão**, **entregue** e **concluído**.

### 4.5 Serviços e conteúdo

- cadastro, edição, publicação e despublicação dos serviços do site;
- descrição curta, funcionalidades, tags e ordem de exibição;
- gerenciamento de portfólio e projetos exibidos publicamente;
- edição de textos institucionais;
- gerenciamento de perguntas frequentes.

### 4.6 Financeiro

- receita total, despesas, lucro líquido e inadimplência;
- evolução da receita por mês;
- distribuição das despesas por categoria;
- cadastro e consulta de transações;
- identificação de cliente ou fornecedor;
- receitas e despesas;
- situações de pagamento como pago, pendente e atrasado.

O módulo oferece uma visão gerencial da operação. Regras contábeis, fiscais,
conciliação bancária e emissão de documentos fiscais não estão definidas no
Figma e não devem ser presumidas.

### 4.7 Configurações

- perfil e dados pessoais;
- informações da empresa;
- preferências de idioma, fuso horário e tema;
- notificações;
- segurança da conta;
- integrações;
- foto de perfil;
- consulta e encerramento de sessões ativas;
- alteração de senha e autenticação em dois fatores.

## 5. Fluxos principais

### 5.1 Do contato ao projeto

1. O visitante conhece os serviços ou projetos da Deploy.
2. Ele envia um contato ou solicita um orçamento.
3. A solicitação entra na área de leads e orçamentos.
4. A equipe analisa a necessidade, registra interações e envia uma proposta.
5. Quando a proposta é aceita, o lead é convertido em projeto.
6. O projeto passa a ser acompanhado por situação, prazo, valor e progresso.

### 5.2 Da execução ao acompanhamento financeiro

1. A equipe acompanha o projeto até sua entrega.
2. Receitas e despesas relacionadas são registradas no financeiro.
3. O dashboard consolida os principais indicadores comerciais, operacionais e
   financeiros.

### 5.3 Da administração ao site público

1. A equipe cadastra ou edita um serviço, projeto de portfólio, texto ou FAQ.
2. O conteúdo é revisado e publicado.
3. O site institucional passa a apresentar a informação aprovada.

## 6. Regras de escopo

- O Figma é a referência funcional e visual inicial do produto.
- Exemplos de pessoas, empresas, valores, datas e tecnologias presentes no
  design são dados ilustrativos, não dados reais nem regras fixas.
- Um item documentado não equivale a uma funcionalidade implementada.
- Novas regras de negócio precisam ser aprovadas e registradas antes da
  implementação.
- Integrações externas, níveis de permissão, notificações automáticas, regras
  fiscais e critérios detalhados de cálculo dependem de definição posterior.

## 7. Fora do escopo desta atualização

Esta atualização é exclusivamente documental. Ela não inclui:

- criação ou alteração de telas;
- mudanças em banco de dados, migrations ou seeders;
- criação de APIs, controllers, models ou regras de negócio;
- implementação de autenticação, permissões ou integrações;
- alteração do arquivo do Figma;
- remoção automática da fatia demonstrativa existente no código.
