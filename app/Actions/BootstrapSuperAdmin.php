<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class BootstrapSuperAdmin
{
    public function __construct(
        private DatabaseManager $database,
        private PasswordBroker $passwordBroker,
    ) {}

    /** @return array{user: User, created: bool} */
    public function handle(string $name, string $email): array
    {
        return $this->database->transaction(function () use ($name, $email): array {
            $role = Role::query()
                ->where('name', 'super_admin')
                ->lockForUpdate()
                ->sole();

            throw_if($role->users()->exists(), RuntimeException::class, 'O bootstrap foi recusado porque o sistema já possui um Super Admin.');

            $user = User::query()->firstOrCreate(
                ['email' => Str::lower(trim($email))],
                [
                    'name' => trim($name),
                    'password' => Str::random(64),
                ],
            );

            $status = $this->passwordBroker->sendResetLink(['email' => $user->email]);

            throw_if($status !== PasswordBroker::RESET_LINK_SENT, RuntimeException::class, 'Não foi possível enviar o convite seguro. Nenhum Super Admin foi atribuído.');

            $user->roles()->syncWithoutDetaching([$role->id]);

            return ['user' => $user->load('roles'), 'created' => $user->wasRecentlyCreated];
        });
    }
}
