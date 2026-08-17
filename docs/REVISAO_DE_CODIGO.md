# Revisão de código

Duas partes: **como revisar** (o que olhar, nesta ordem) e **como se comportar**
(o que escrever, e como). A segunda é a que faz o time durar.

---

## Parte 1 — O que o CI já revisou (não gaste review nisso)

Antes de abrir o diff, saiba o que já está garantido. Comentar sobre isso
desperdiça o seu tempo e o de quem escreveu:

| Já verificado por máquina | Onde |
|---|---|
| Formatação, ordem de imports, `strict_types` | Pint |
| Tipo, `null`, método inexistente, retorno mentiroso | PHPStan nível 8 |
| Camadas, `final`, Action com um `handle()`, `env()` fora de `config/`, debug esquecido | testes de arquitetura |
| Cobertura mínima de 80% | Pest no CI |
| Formato da mensagem de commit e do título do PR | CaptainHook + workflow |
| Segredo no diff, arquivo gigante, marcador de conflito | hook de pre-commit |
| nginx, FPM, MySQL, Redis, migrations e rotas de verdade | job de smoke |
| Imagem de produção sem dev, sem Node, sem `.env`, com cache | job de produção |

**Seu trabalho é o que a máquina não vê**: se a mudança resolve o problema certo,
se ela vai ser entendida em seis meses, e se ela abre um buraco.

---

## Parte 2 — A ordem da revisão

Revise nesta ordem. Ela é deliberada: os itens de cima invalidam os de baixo.
Não há razão para discutir nome de variável em código que não deveria existir.

### 1. Entenda o objetivo antes do diff

Leia título e descrição. Se você não conseguir dizer em uma frase o que o PR
tenta resolver, **esse é o primeiro comentário** — e é um comentário sobre a
descrição, não sobre o código.

### 2. A solução resolve o problema?

- Resolve a causa ou o sintoma?
- Faz o que o PR diz que faz — e só isso? Mudança fora do escopo declarado é o
  jeito mais comum de um bug entrar sem ninguém olhar.
- Existe caminho notavelmente mais simples? Diga qual, não apenas "dá para
  simplificar".

### 3. Correção nos casos que ninguém testou

Aqui é onde review encontra bug de verdade. Pergunte-se, para cada caminho:

- **Vazio / ausente:** lista sem itens, campo opcional não enviado, `null`.
- **Limite:** primeiro, último, zero, negativo, ano 1900, string no máximo.
- **Duplicado:** placa que já existe, mesma requisição duas vezes (o botão que a
  pessoa clica em dobro).
- **Permissão:** anônimo, autenticado sem direito, dono de outro recurso. Todo
  endpoint novo tem alguém que **não** deveria conseguir usá-lo — esse teste
  existe?
- **Erro do meio:** o que fica no banco se a segunda operação falhar? Precisa de
  transação?
- **Consulta em laço:** `foreach` que carrega relação a cada volta (N+1). Custa
  nada agora e derruba a tela com 500 registros.

### 4. Segurança

- Model devolvido direto pela API em vez de Resource (vaza toda coluna).
- `$request->all()` alimentando `create()`/`update()` em vez de `validated()`.
- Autorização ausente: middleware de autenticação **não** é autorização.
- Query montada por concatenação de string em vez de binding.
- Dado sensível em log ou em mensagem de erro devolvida ao cliente.

### 5. Testes

- Existe teste para o comportamento novo?
- O teste **falharia** se o código estivesse errado? Teste que só chama e não
  verifica nada passa a cobertura e não protege ninguém.
- Bug corrigido tem teste que reproduz o bug?
- O nome do teste diz o comportamento esperado, não o método chamado.

### 6. Legibilidade e manutenção

- Nome revela intenção.
- Comentário explica **por quê**; o que descreve o *quê* deveria ser um nome
  melhor.
- Duplicação que vai divergir (a mesma regra em dois lugares). Duplicação que não
  vai divergir pode ficar — abstração prematura custa mais.
- Decisão estrutural merece [ADR](adr/README.md)?

### 7. Só então, detalhe

Preferência de estilo que o Pint não cobra, ordem de métodos, sinônimo melhor
para um nome. **Marque como opcional.** Ver a seção seguinte.

---

## Parte 3 — Como escrever o comentário

O objetivo do review é o código melhor **e** a pessoa mais capaz na próxima vez.
Um review que consegue o primeiro e destrói o segundo é um review malfeito.

### Classifique cada comentário

Prefixo explícito, sempre. Sem ele, quem recebe trata tudo como bloqueio — ou
ignora tudo.

- **`bloqueio:`** precisa mudar antes do merge. Bug, falha de segurança, ausência
  de teste em código novo.
- **`sugestão:`** melhoraria, e você aceita "não" como resposta.
- **`opcional:`** gosto pessoal. Nunca segura um merge.
- **`pergunta:`** você não entendeu. Talvez o problema seja o código; talvez seja
  você. As duas descobertas são úteis.
- **`elogio:`** sim, isto é parte do trabalho. Quando alguém resolve bem, diga —
  é assim que o padrão se propaga. Review que só aponta erro ensina a pessoa a
  temer o review, não a escrever melhor.

### Como escrever

- **Comente o código, nunca a pessoa.** "esta função faz duas coisas", não "você
  não sabe separar responsabilidade".
- **Explique o porquê.** "use `validated()`: `all()` aceita qualquer campo que o
  cliente inventar" ensina; "use `validated()`" apenas corrige.
- **Aponte o caminho.** Se sabe como resolver, diga como. Review não é enigma.
- **Uma linha de comentário por problema.** Cinco problemas em um comentário
  gigante viram um problema resolvido e quatro esquecidos.
- **Duas idas e voltas no mesmo ponto = hora de conversar.** Texto assíncrono é
  ruim para desentendimento; cinco minutos de conversa resolvem o que dez
  comentários não resolvem.
- **Aprove com ressalva pequena.** Se só restam `opcional:`, aprove e confie. PR
  travado por gosto pessoal ensina a pessoa a evitar PRs, não a melhorar.

### Se você recebeu o review

- Comentário no seu código não é comentário sobre você. É a razão de existir o
  review: código lido por duas pessoas tem menos bug que código lido por uma.
- Responda a todos. "Feito" basta; discordar também é resposta válida — traga o
  motivo, não a defesa.
- Não entendeu? Pergunte. Aplicar uma mudança sem entender troca um erro por
  outro, e você perde o único momento em que aquilo seria explicado.
- Ao discordar, ganha o argumento melhor, não o cargo mais alto. Se não houver
  convergência, chame quem tem contexto — e registre a conclusão (em ADR, se for
  estrutural).

---

## Prazo

Revise em **um dia útil**. PR aberto é trabalho parado e é a coisa mais frustrante
da experiência de quem está começando: sem retorno, ele começa a supor que fez
algo errado.

Sem tempo hoje? Diga isso no PR. "só consigo olhar amanhã de manhã" é uma
resposta perfeitamente boa; silêncio não é.
