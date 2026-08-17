# 0003 — PHPStan (Larastan) no nível 8, sem baseline

- **Estado:** Aceita
- **Data:** 2026-08-17 (registro de decisão tomada no início do projeto)
- **Decide:** Miguel

## Contexto

Análise estática pega, antes de qualquer teste rodar, a classe de erro mais comum
em PHP: variável que pode ser `null`, tipo que não é o esperado, método que não
existe, retorno que mente sobre si mesmo. É a verificação de menor custo e maior
retorno que existe.

Duas escolhas precisam ser feitas ao adotá-la, e as duas definem se ela vai
servir para algo:

1. **O nível.** Baixo (0–4) aceita quase tudo e dá falsa sensação de segurança.
2. **O baseline.** É um arquivo que lista os erros existentes e manda o PHPStan
   ignorá-los. Serve para adotar análise estática em código legado sem paralisar
   o time.

O projeto **não tem legado**: começou agora.

## Decisão

Nível **8**, e **nenhum baseline**.

- `level: 8` em `phpstan.neon`, sobre `app`, `config`, `database`, `routes` e
  `tests`.
- Nenhum `baseline.neon`. Erro novo falha o hook de pre-commit e falha o CI.
- `reportUnmatchedIgnoredErrors: true`: um `ignoreErrors` que deixou de ser
  necessário passa a ser erro. É isso que impede a lista de ignores de virar
  lixeira silenciosa.
- Cada `ignoreErrors` é preso a um caminho e a uma classe/identificador exatos, e
  vem com o motivo escrito acima dele. Os que existem hoje são falso positivo do
  Pest e stub publicado por pacote — nenhum é código nosso.

Baixar o nível para fazer um PR passar é resposta errada. Se a análise reclama,
ou o código está ambíguo (corrija o código), ou o tipo não foi declarado
(declare), ou é falso positivo genuíno (ignore específico, com motivo).

## Alternativas consideradas

- **Nível 5–6 "para não travar o time".** Perde exatamente as verificações de
  `null` e de tipo de argumento, que são as que pegam bug real. Sobra o
  cerimonial.
- **Nível 8 com baseline.** Em projeto novo, baseline é lixeira: o primeiro erro
  chato entra nela e a análise passa a mentir. Baseline é dívida registrada de
  código legado, não gaveta para erro recém-escrito.
- **Nível 9/10.** O ganho marginal vira briga com o Eloquent e com arrays
  dinâmicos do framework. O custo sobe mais rápido que o benefício, e o time é
  júnior.

## Consequências

**Ganhos:** bug de tipo e de `null` é pego no pre-commit, não em produção; o
código fica anotado (`@return array<string, string>` etc.), o que também
documenta; não existe dívida acumulada silenciosa.

**Custos:**

- Escrever PHP anotado é mais lento no começo, e a mensagem do PHPStan é hostil
  para quem nunca a leu. É um custo de aprendizado real para júnior — e um dos
  motivos de este ADR existir.
- Alguns padrões idiomáticos do Laravel exigem anotação extra para passar.
- A análise leva alguns segundos a cada commit.

**Como sabemos que deu errado:** se a lista de `ignoreErrors` começar a crescer,
ou se alguém propor baixar o nível, o problema não é o PHPStan — é código com
tipo ambíguo. Um `ignoreErrors` sem motivo escrito acima é sinal claro.
