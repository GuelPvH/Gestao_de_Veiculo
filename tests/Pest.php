<?php

declare(strict_types=1);

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Bindings por diretório
|--------------------------------------------------------------------------
|
| `RefreshDatabase` é aplicado POR DIRETÓRIO, e não com `uses()` repetido no
| topo de cada arquivo de teste. Esse é o principal motivo de este arquivo
| existir: um esquecimento em um único arquivo produz teste que vaza estado
| para o seguinte, e o sintoma aparece longe da causa.
|
| `tests/Unit` fica de fora de propósito: teste unitário que precisa de banco
| não é unitário, e amarrá-lo ao TestCase do Laravel esconderia isso.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations de domínio
|--------------------------------------------------------------------------
|
| Evitam a mesma asserção repetida em dezenas de arquivos e dão uma mensagem
| de falha que fala a linguagem do problema, não a da regex.
|
*/

expect()->extend('toBeValidPlate',
    // Mercosul (LLLNLNN) ou o padrão antigo (LLLNNNN).
    fn () => $this->toMatch('/^([A-Z]{3}\d[A-Z0-9]\d{2}|[A-Z]{3}\d{4})$/'));

/*
|--------------------------------------------------------------------------
| Helpers globais
|--------------------------------------------------------------------------
*/

/**
 * @return Collection<int, Vehicle>
 */
function createFleet(int $count = 3): Collection
{
    return Vehicle::factory()->count($count)->create();
}
