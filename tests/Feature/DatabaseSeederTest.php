<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Seeder
|--------------------------------------------------------------------------
|
| O `db:seed` só era exercitado no smoke test da stack completa, que leva
| minutos e roda depois de subir Docker inteiro. Um seeder quebrado (foi o
| caso do `password` ausente, que a coluna NOT NULL rejeitava) aparecia lá,
| e não aqui. Estes testes trazem a falha para a suíte de segundos.
|
*/

it('semeia o usuario administrador com credenciais utilizaveis', function (): void {
    $this->seed(DatabaseSeeder::class);

    $user = User::query()->where('email', 'teste@software-house.test')->sole();

    expect($user->isSuperAdmin())->toBeTrue()
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

it('semeia a frota de exemplo', function (): void {
    $this->seed(DatabaseSeeder::class);

    expect(Vehicle::query()->count())->toBe(15);
});

it('pode ser executado de novo sem duplicar nada', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())->toBe(1)
        ->and(Vehicle::query()->count())->toBe(15);
});
