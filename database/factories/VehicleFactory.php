<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
final class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate' => $this->mercosulPlate(),
            'brand' => fake()->randomElement(['Fiat', 'Volkswagen', 'Chevrolet', 'Toyota', 'Renault']),
            'model' => fake()->randomElement(['Strada', 'Saveiro', 'Onix', 'Hilux', 'Kwid']),
            'year' => fake()->numberBetween(2015, 2026),
            'status' => fake()->randomElement(VehicleStatus::cases()),
        ];
    }

    public function available(): self
    {
        return $this->state(fn (): array => ['status' => VehicleStatus::Available]);
    }

    public function inMaintenance(): self
    {
        return $this->state(fn (): array => ['status' => VehicleStatus::Maintenance]);
    }

    /**
     * Placa no padrão Mercosul: LLLNLNN.
     *
     * `bothify()` aceita UM argumento — o segundo, com o alfabeto, era
     * silenciosamente descartado. `?` já sorteia letra (minúscula), e o
     * mb_strtoupper normaliza.
     */
    private function mercosulPlate(): string
    {
        return mb_strtoupper(fake()->unique()->bothify('???#?##'));
    }
}
