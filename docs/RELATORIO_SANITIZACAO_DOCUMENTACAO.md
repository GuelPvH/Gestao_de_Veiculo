# Relatório de sanitização e proteção de credenciais

Data da revisão: 31 de agosto de 2026.

## Objetivo

Preparar o repositório para publicação sem divulgar credenciais, endereços
internos, dados pessoais ou detalhes operacionais que facilitem acesso indevido.
Este relatório não reproduz nenhum valor removido.

## Conteúdo removido ou generalizado

- referências ao escopo antigo de gestão de veículos e de frota, substituídas
  pelo escopo da Software House Deploy na Sexta;
- instruções com endereço, porta ou link direto de banco de dados e de painéis
  administrativos;
- exemplos de usuário, e-mail e senha de acesso;
- strings de conexão, DSNs, tokens, chaves e valores de variáveis sensíveis;
- referências a documentos, ambientes ou serviços internos que não devem fazer
  parte de um repositório público;
- detalhes de infraestrutura que não eram necessários para compreender o
  projeto publicamente.

Os exemplos públicos passaram a usar nomes de variáveis vazios ou placeholders
descritivos. O arquivo local `.env` não foi alterado: ele está ignorado pelo Git
e não está versionado.

## Arquivos de documentação revisados

- `README.md`;
- `CONTRIBUTING.md`;
- `SECURITY.md`;
- `.github/PULL_REQUEST_TEMPLATE.md`;
- `docs/ARQUITETURA.md`;
- `docs/DEFINICAO_DE_PRONTO.md`;
- `docs/ESCOPO.md`;
- `docs/GLOSSARIO.md`;
- `docs/INSTALACAO.md`;
- `docs/RECEITA_NOVO_RECURSO.md`;
- `.env.example`, apenas como modelo público sem valores sensíveis.

## Proteções adicionadas

- workflow separado para varredura em pull requests, branches principais,
  execução manual e agenda semanal;
- Gitleaks com regras padrão e regras específicas para o projeto;
- varredura do histórico completo do Git com valores redigidos nos logs;
- execução do scanner em imagem fixada por digest, sem rede, sem capacidades
  Linux, com filesystem somente leitura e sem receber token ou licença;
- bloqueio de `.env`, dumps, bancos locais, chaves privadas e arquivos comuns de
  autenticação;
- validação de campos sensíveis no `.env.example` e de autenticação no `.npmrc`;
- exigência de SHA imutável para Actions externas e digest para Actions Docker;
- permissões mínimas de leitura para os workflows e checkout sem persistência
  da credencial do GitHub;
- hook local de pre-commit e status check obrigatório previsto na configuração
  de proteção da branch;
- revisão obrigatória dos arquivos de configuração do scanner via CODEOWNERS.

## Exceções históricas

Três achados históricos já conhecidos foram classificados como valores de
exemplo ou credenciais efêmeras exclusivas do antigo runner de CI. As exceções
usam fingerprints exatos — commit, arquivo, regra e linha — para não ocultar
novas ocorrências semelhantes.

## Validação executada

- sintaxe de Bash, JSON, TOML e YAML;
- verificação de whitespace com `git diff --check`;
- auditoria da política de arquivos sensíveis;
- varredura Gitleaks dos 25 commits do histórico, sem vazamentos após a
  classificação dos falsos positivos;
- varredura do diretório atual, na qual somente o `.env` local e ignorado foi
  identificado, confirmando que ele contém dados que não devem ser versionados.

Qualquer credencial real que tenha sido publicada no passado deve ser revogada
e rotacionada. Apagar o texto de um commit novo não invalida uma credencial já
exposta nem remove automaticamente o conteúdo do histórico remoto.
