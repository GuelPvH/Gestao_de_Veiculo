<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;

final readonly class SummariseFleet
{
    /**
     * @return array{total: int, por_status: array<string, int>}
     */
    public function handle(): array
    {
        $porStatus = [];

        foreach (VehicleStatus::cases() as $status) {
            $porStatus[$status->value] = Vehicle::query()
                ->where('status', $status)
                ->count();
        }

        return [
            'total' => Vehicle::query()->count(),
            'por_status' => $porStatus,
        ];
    }
}
