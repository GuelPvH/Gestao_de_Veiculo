<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Usuario de Teste',
            'email' => 'teste@gestao-veiculo.test',
        ]);

        Vehicle::factory()->count(12)->create();
        Vehicle::factory()->count(3)->inMaintenance()->create();
    }
}
