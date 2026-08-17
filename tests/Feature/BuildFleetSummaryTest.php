<?php

declare(strict_types=1);

use App\Actions\SummariseFleet;
use App\Enums\VehicleStatus;
use App\Jobs\BuildFleetSummary;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

it('resume a frota por status e grava em cache', function (): void {
    Vehicle::factory()->count(2)->available()->create();
    Vehicle::factory()->inMaintenance()->create();

    resolve(BuildFleetSummary::class)->handle(resolve(SummariseFleet::class));

    $summary = Cache::get(BuildFleetSummary::CACHE_KEY);

    expect($summary)->toBeArray()
        ->and($summary['total'])->toBe(3)
        ->and($summary['por_status'][VehicleStatus::Available->value])->toBe(2)
        ->and($summary['por_status'][VehicleStatus::Maintenance->value])->toBe(1)
        ->and($summary['gerado_em'])->toBeString();
});

it('enfileira o job pelo comando artisan', function (): void {
    Queue::fake();

    $this->artisan('frota:resumo')
        ->expectsOutputToContain('BuildFleetSummary')
        ->assertSuccessful();

    Queue::assertPushed(BuildFleetSummary::class);
});
