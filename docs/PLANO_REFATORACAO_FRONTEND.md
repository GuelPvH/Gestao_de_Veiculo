# Plano de refatoração do front-end

Este documento acompanha a refatoração incremental das views Blade, do SCSS e
do JavaScript. Cada etapa deve preservar o comportamento observável e entrar em
um commit com no máximo cinco arquivos alterados.

## Objetivos

- separar estilos de fundação, layout, componentes e páginas;
- consolidar componentes Blade reutilizáveis sem criar abstrações genéricas;
- remover dados demonstrativos e regras de apresentação das views;
- organizar comportamentos JavaScript por atributos `data-*`;
- ampliar os gates de qualidade do front-end.

## Ordem de execução

1. Testes de caracterização das telas existentes.
2. Tokens e divisão do `app.scss`.
3. Layout-base e shell administrativo.
4. Componentes compartilhados de interface e formulário.
5. Migração das telas de configurações.
6. Dados do dashboard fora dos componentes.
7. Dados e contrato dos cards de projetos.
8. JavaScript declarativo e remoção de eventos inline.
9. Lint, testes e documentação final.

## Restrições

- não misturar funcionalidades novas com refatoração estrutural;
- não passar Models Eloquent diretamente para componentes visuais;
- não introduzir framework JavaScript sem uma decisão arquitetural própria;
- manter textos visíveis em português e identificadores de código em inglês;
- executar `make check` e o build do Vite antes da entrega.
