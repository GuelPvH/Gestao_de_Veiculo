# Política de segurança

## Reportando uma vulnerabilidade

**Não abra issue pública para vulnerabilidade.** Uma issue é visível para
qualquer pessoa, inclusive para quem exploraria a falha antes da correção.

Use o canal privado do GitHub: aba **Security → Report a vulnerability**
(*Private vulnerability reporting*). Se estiver indisponível, entre em contato
com a pessoa responsável pelo repositório.

Inclua no relato:

- O que a falha permite fazer
- Passo a passo para reproduzir
- Versão / commit afetado
- Impacto que você enxerga

## Se você vazou uma credencial

Acontece. O importante é a ordem das ações:

1. **Revogue/rotacione a credencial primeiro.** Remover o commit não basta — o
   valor já esteve exposto e pode ter sido coletado.
2. Só depois limpe o histórico, se for o caso.
3. Avise o time.

Um segredo que foi para um repositório remoto deve ser considerado comprometido,
mesmo que o push tenha sido revertido em seguida.

## Práticas adotadas neste projeto

- `.env` fora do Git; `.env.example` traz as chaves com valores vazios
- Hook `pre-commit` com detecção de segredo (chave AWS, token GitHub/Google,
  senha) — rede de segurança, não substituto de cuidado
- `composer audit` roda no CI e **bloqueia** o merge; `npm audit` é consultivo
- Dependabot abre PR semanal para Composer, npm, Dockerfiles e GitHub Actions
- Análise estática em nível 8, sem baseline
- Arch tests barram `env()` fora de `config/` e `dd()`/`dump()` esquecidos
- API com rate limit em todas as rotas, inclusive autenticação
- painéis de observabilidade protegidos por autorização administrativa
- API responde via Resource, nunca com o Model direto

## Proteções automatizadas contra vazamento

O workflow `.github/workflows/secret-scan.yml` roda em pull requests, pushes nas
branches principais, execução manual e agenda semanal. Ele:

- faz checkout do histórico completo sem persistir a credencial do GitHub;
- bloqueia `.env`, dumps, bancos locais, chaves privadas e arquivos conhecidos
  de autenticação;
- valida que `.env.example` não recebeu valor em campo sensível;
- valida que `.npmrc` não contém autenticação;
- exige SHA completo e imutável em todas as Actions externas;
- executa Gitleaks sobre o histórico Git em imagem fixada por digest;
- executa o scanner sem rede, com filesystem somente leitura, sem capacidades
  Linux e sem fornecer token ou licença ao processo;
- redige os valores encontrados e não publica comentários nem artefatos.

As exceções em `.gitleaksignore` devem usar fingerprint completo e só podem ser
incluídas depois de o achado ser classificado sem revelar o valor. Uma exceção
por regex, diretório ou regra inteira é proibida porque também esconderia
vazamentos futuros.

## Higiene para repositório público

Não devem ser versionados, inclusive em arquivos Markdown:

- usuários, e-mails ou senhas de acesso, mesmo que sejam de teste;
- tokens, chaves, cookies, DSNs ou strings de conexão;
- endereços e portas de painéis administrativos ou bancos de dados;
- links de documentos internos, arquivos privados de design ou dashboards;
- dados pessoais, nomes de clientes ou informações comerciais reais;
- saídas de terminal que contenham variáveis de ambiente.

Use placeholders descritivos, como `<usuario-local>` e `<endereco-interno>`, sem
copiar o valor real. Nomes de variáveis vazias em `.env.example` podem ser
versionados; os valores devem vir do ambiente local ou do gerenciador de segredos.

## Limites conhecidos do ambiente de desenvolvimento

Aceitáveis **apenas em desenvolvimento**. Não replique em produção:

- **serviços locais simplificados** — precisam receber autenticação e isolamento
  apropriados antes de qualquer implantação
- **`APP_DEBUG=true`** — expõe stack trace detalhado
- **Debugbar habilitada** — expõe queries e dados de request
- **Ferramentas administrativas de desenvolvimento** — devem permanecer
  restritas à máquina local e nunca ter endereço, porta ou credencial publicados

Em produção: `APP_DEBUG=false`, serviços internos autenticados, ferramentas de
desenvolvimento desativadas, segredos fornecidos pelo ambiente de execução e
backup apontando para armazenamento remoto autorizado.
