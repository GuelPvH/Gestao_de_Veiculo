# Glossário e convenção de idioma

Duas coisas que parecem burocracia e economizam horas de review: **um nome só
para cada conceito** e **uma regra clara de quando escrever em inglês ou em
português**.

Sem isso, o mesmo conceito aparece como `Veiculo`, `Vehicle`, `Carro` e `Auto` em
quatro arquivos — e ninguém consegue mais buscar nada no projeto.

---

## 1. Regra de idioma

| O que | Idioma | Por quê |
|---|---|---|
| Nome de classe, método, variável, tabela, coluna | **inglês** | É o idioma do framework e das bibliotecas. Misturar produz `BuscaVeiculoRepository::getVeiculos()` chamando `Vehicle::all()` na linha seguinte. |
| Nome de rota e caminho de URL | **inglês** | `/api/vehicles`, `vehicles.index`. Consistente com o resto do código. |
| Valor de enum persistido no banco | **português** | Já é o caso (`disponivel`, `em_uso`). É dado, não código, e aparece em relatório. |
| Comentário e docblock | **português** | O time todo lê português; o custo de escrever inglês mediano é uma explicação pior. |
| Mensagem exibida a pessoa (API, tela, e-mail) | **português** | É produto. `'Veículo removido com sucesso.'` |
| Mensagem de commit, PR, issue, documentação | **português** | Mesmo motivo. O *tipo* do commit segue Conventional Commits e fica em inglês (`feat`, `fix`). |
| Nome de teste | **português** | `it('rejeita placa já cadastrada')` descreve comportamento para quem lê o relatório. |

**Regra de bolso:** se um computador lê, inglês. Se uma pessoa lê, português.

Nunca misture os dois em um mesmo identificador: `getVeiculo()`,
`VeiculoController` e `$listaDeVehicles` estão todos errados.

---

## 2. Termos do domínio

Estes são os nomes **certos**. Ao criar código novo, use exatamente estes.

| Conceito (pt) | No código (en) | O que é |
|---|---|---|
| Veículo | `Vehicle` | A unidade central do sistema: um carro/utilitário da frota. Model Eloquent em `app/Models/Vehicle.php`. |
| Frota | `Fleet` | O conjunto dos veículos. Não existe tabela `fleets`: "frota" é o agregado, aparece em operações como `SummariseFleet`. |
| Placa | `plate` | Identificador único do veículo. Padrão Mercosul (`ABC1D23`) ou antigo (`ABC1234`). É a chave natural — é por ela que uma pessoa procura um veículo. |
| Marca | `brand` | Fabricante: Fiat, Volkswagen. |
| Modelo (do veículo) | `model` | Strada, Onix. **Cuidado:** "Model" com M maiúsculo é a classe Eloquent. Ver a nota abaixo. |
| Ano | `year` | Ano do veículo. Inteiro, entre 1900 e o ano que vem. |
| Situação | `status` | Estado atual do veículo. Enum `VehicleStatus`, nunca string solta. |
| Disponível | `VehicleStatus::Available` (`disponivel`) | Livre para uso. |
| Em uso | `VehicleStatus::InUse` (`em_uso`) | Alocado no momento. |
| Em manutenção | `VehicleStatus::Maintenance` (`manutencao`) | Indisponível por manutenção. |
| Resumo da frota | `FleetSummary` | Agregado calculado (contagem por situação). Produzido pela Action `SummariseFleet` e pelo Job `BuildFleetSummary`. |

### A ambiguidade de "model"

`model` (minúsculo) é o modelo do veículo — uma coluna de texto. `Model`
(maiúsculo) é a classe Eloquent. São coisas diferentes no mesmo projeto e a
confusão é inevitável; o que resolve é o contexto do nome:

```php
$vehicle->model              // ✅ string: "Onix"
VehicleResource::make($v)    // ✅ o Model é a classe, não um campo
$modelName = 'Onix';         // ❌ ambíguo — prefira $vehicleModel ou só model
```

Em prosa e em comentário, escreva **"modelo do veículo"** ou **"Model do
Eloquent"**. Nunca só "modelo".

---

## 3. Nomes de estrutura (as convenções que o projeto cobra)

Estas não são preferência: os testes de arquitetura quebram se forem violadas.

| Camada | Padrão de nome | Exemplo |
|---|---|---|
| Action | verbo + substantivo, imperativo | `ListVehicles`, `SummariseFleet` |
| Controller | recurso + `Controller` | `VehicleController`, `VehicleApiController` |
| FormRequest | operação + recurso + `Request` | `StoreVehicleRequest`, `UpdateVehicleRequest` |
| Policy | recurso + `Policy` | `VehiclePolicy` (o nome é o que o Laravel descobre — não invente) |
| Resource | recurso + `Resource` | `VehicleResource` |
| Job | verbo + substantivo | `BuildFleetSummary` |
| Enum | recurso + atributo | `VehicleStatus` |
| Migration | ação + tabela, em inglês | `create_vehicles_table`, `add_is_admin_to_users_table` |
| Teste de feature | recurso + assunto + `Test` | `VehicleApiTest` |

Nomes proibidos, porque não dizem nada: `Manager`, `Handler`, `Helper`,
`Util(s)`, `Service` genérico, `data`, `info`, `temp`, `aux`, `x`.

---

## 4. Ao adicionar um termo novo

Conceito de domínio novo (motorista, viagem, abastecimento, manutenção) **entra
nesta tabela no mesmo PR** que o cria. É trinta segundos de trabalho e evita que
"abastecimento" seja `Refuel` em um arquivo e `FuelSupply` em outro.

Se o termo em inglês não for óbvio, decida no PR — e a decisão está tomada para
sempre a partir dali.
