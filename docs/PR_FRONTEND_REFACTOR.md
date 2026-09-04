# Refatoração do front-end

## O que muda

Refatora o front-end Laravel, consolidando layouts Blade, componentes reutilizáveis de UI/formulários e a organização do SCSS em módulos de fundação, layout e páginas.

Também adiciona testes de caracterização para o painel administrativo e inicia a extração dos dados demonstrativos das views para ViewModels e controllers.

## Por quê

As views possuíam muita duplicação de markup, estilos inline, dados fictícios dentro dos componentes e dois layouts HTML independentes.

A mudança cria uma base reutilizável para novas telas, reduz o acoplamento entre Blade e dados, melhora a consistência visual e facilita a manutenção do front-end.

## Como testar

1. No PowerShell, execute:

   ```powershell
   .\make.ps1 up
   ```

2. Acesse `/login`, `/admin/dashboard`, `/admin/projetos` e as páginas de configurações.
3. Confirme que layouts, navegação, cards, formulários, métricas e responsividade continuam funcionando.
4. Execute:

   ```powershell
   .\make.ps1 artisan c="view:cache"
   .\make.ps1 test
   .\make.ps1 analyse
   .\make.ps1 npm c="run build"
   ```

## Checklist

- [ ] `make check` passa localmente
- [ ] `make test-coverage` passa — o CI reprova abaixo de 80%
- [x] Adicionei testes de caracterização para as telas administrativas
- [ ] Se corrige um bug: existe um teste que falhava antes e passa agora
- [ ] O teste falharia se o código estivesse errado
- [x] Nenhuma variável de ambiente foi adicionada
- [x] Nenhuma credencial real foi commitada
- [ ] Termo novo do domínio registrado em `docs/GLOSSARIO.md`
- [ ] Decisão estrutural nova registrada em `docs/adr/`
- [ ] Entrada no `CHANGELOG.md`, em `Não lançado`
- [x] Documentação atualizada em `docs/PLANO_REFATORACAO_FRONTEND.md`
- [x] Uma coisa por PR: refatoração estrutural do front-end

## Impacto

- [ ] Precisa rodar migration (`make migrate`)
- [ ] Precisa de variável de ambiente nova
- [ ] Muda contrato da API
- [x] Nada disso — mudança isolada no front-end e na organização das views
