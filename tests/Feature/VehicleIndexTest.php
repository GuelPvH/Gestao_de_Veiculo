<?php

declare(strict_types=1);

use App\Enums\VehicleStatus;
use App\Models\Vehicle;

it('renderiza a frota na pagina inicial', function (): void {
    $vehicle = Vehicle::factory()->available()->create([
        'plate' => 'ABC1D23',
        'brand' => 'Fiat',
        'model' => 'Strada',
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee($vehicle->plate)
        ->assertSee('Fiat')
        ->assertSee('Strada')
        // O select do filtro precisa sair com o data-attribute, senao o
        // Tom Select nunca é inicializado no navegador.
        ->assertSee('data-tom-select', escape: false);
});

it('filtra a frota pelo status informado', function (): void {
    $available = Vehicle::factory()->available()->create(['plate' => 'AAA1A11']);
    $maintenance = Vehicle::factory()->inMaintenance()->create(['plate' => 'BBB2B22']);

    $response = $this->get('/?status='.VehicleStatus::Maintenance->value);

    $response->assertOk()
        ->assertSee($maintenance->plate)
        ->assertDontSee($available->plate);
});

it('ignora um status invalido em vez de quebrar', function (): void {
    createFleet(2);

    $this->get('/?status=nao-existe')->assertOk();
});

it('gera placas validas na factory', function (): void {
    foreach (createFleet(5) as $vehicle) {
        expect($vehicle->plate)->toBeValidPlate();
    }
});
