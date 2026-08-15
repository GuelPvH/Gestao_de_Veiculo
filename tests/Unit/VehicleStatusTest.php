<?php

declare(strict_types=1);

use App\Enums\VehicleStatus;

it('expoe um rotulo legivel para todo status', function (VehicleStatus $status): void {
    expect($status->label())->toBeString()->not->toBeEmpty();
})->with(VehicleStatus::cases());

it('mapeia todo status para um badge do bootstrap', function (VehicleStatus $status): void {
    expect($status->badgeClass())->toStartWith('text-bg-');
})->with(VehicleStatus::cases());

it('resolve o status a partir do valor persistido', function (): void {
    expect(VehicleStatus::tryFrom('manutencao'))->toBe(VehicleStatus::Maintenance)
        ->and(VehicleStatus::tryFrom('inexistente'))->toBeNull();
});
