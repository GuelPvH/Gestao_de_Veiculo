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
- `/pulse` e `/horizon` protegidos por gate de administrador
- API responde via Resource, nunca com o Model direto

## Limites conhecidos do ambiente de desenvolvimento

Aceitáveis **apenas em desenvolvimento**. Não replique em produção:

- **Redis sem senha** — a porta não é publicada e a rede é uma bridge privada
- **`APP_DEBUG=true`** — expõe stack trace detalhado
- **Debugbar habilitada** — expõe queries e dados de request
- **Mailpit captura todo e-mail** — nada sai de verdade, que é o objetivo aqui
- **phpMyAdmin exposto** em `127.0.0.1` — só alcançável da própria máquina

Em produção: `APP_DEBUG=false`, Redis com senha, sem Debugbar, sem phpMyAdmin,
`SENTRY_LARAVEL_DSN` preenchido e backup apontando para storage remoto.
