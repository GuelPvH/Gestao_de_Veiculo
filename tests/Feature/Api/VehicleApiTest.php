<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Vehicle;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('lista veículos sem autenticação', function (): void {
    Vehicle::factory()->count(3)->create();

    getJson('/api/vehicles')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'plate', 'brand', 'model', 'year', 'status', 'status_label', 'created_at', 'updated_at'],
            ],
        ]);
});

it('exibe um veículo sem autenticação', function (): void {
    $vehicle = Vehicle::factory()->create();

    getJson("/api/vehicles/{$vehicle->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $vehicle->id)
        ->assertJsonPath('data.plate', $vehicle->plate);
});

it('cria veículo com autenticação', function (): void {
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/vehicles', [
        'plate' => 'ABC-1234',
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2024,
        'status' => 'disponivel',
    ])
        ->assertCreated()
        ->assertJsonPath('data.plate', 'ABC-1234');
});

it('impede criação sem autenticação', function (): void {
    postJson('/api/vehicles', [
        'plate' => 'XYZ-9999',
        'brand' => 'Honda',
        'model' => 'Civic',
        'year' => 2024,
    ])->assertUnauthorized();
});

it('atualiza veículo com autenticação', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();

    putJson("/api/vehicles/{$vehicle->id}", [
        'brand' => 'Fiat',
    ])
        ->assertOk()
        ->assertJsonPath('data.brand', 'Fiat');
});

it('remove veículo com autenticação', function (): void {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();

    deleteJson("/api/vehicles/{$vehicle->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Veículo removido com sucesso.');

    expect(Vehicle::find($vehicle->id))->toBeNull();
});

it('nunca expõe campos sensíveis do Resource', function (): void {
    Vehicle::factory()->create();

    $response = getJson('/api/vehicles');

    $vehicleData = $response->json('data.0');
    expect($vehicleData)->not->toHaveKeys(['password', 'remember_token']);
    expect($vehicleData)->toHaveKeys(['id', 'plate', 'brand', 'model', 'year', 'status', 'status_label']);
});
