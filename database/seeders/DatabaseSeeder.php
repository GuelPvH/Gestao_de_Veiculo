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

    /**
     * Idempotente de propósito: `make seed` é um comando que a pessoa roda
     * quando quer dados de exemplo, e não dá para exigir que ela lembre se já
     * rodou antes. Sem isso, a segunda execução estouraria na constraint de
     * e-mail único do usuário e ainda empilharia mais 15 veículos.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'teste@gestao-veiculo.test'],
            ['name' => 'Usuario de Teste'],
        );

        // `is_admin` fica FORA do $fillable de propósito: mass assignment nessa
        // coluna seria escalada de privilégio se algum controller chamasse
        // `User::create($request->all())`. Aqui a atribuição é explícita, que é
        // o único lugar onde ela deve acontecer. Sem isto o usuário semeado não
        // abriria /pulse nem /horizon, que exigem o gate de admin.
        $user->is_admin = true;
        $user->save();

        // Só popula a frota se ela estiver vazia — reexecutar não duplica.
        if (Vehicle::query()->exists()) {
            return;
        }

        Vehicle::factory()->count(12)->create();
        Vehicle::factory()->count(3)->inMaintenance()->create();
    }
}
