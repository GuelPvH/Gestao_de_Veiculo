<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListVehicles
{
    /**
     * @return Collection<int, Vehicle>
     */
    public function handle(?VehicleStatus $status = null): Collection
    {
        $query = Vehicle::query()->orderBy('plate');

        if ($status instanceof VehicleStatus) {
            $query->where('status', $status);
        }

        return $query->get();
    }
}
