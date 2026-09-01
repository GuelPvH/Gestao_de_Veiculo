<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Str;

final readonly class CreateUser
{
    /** @param array{name: string, email: string, role_ids: list<int>} $data */
    public function handle(array $data, User $actor): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'password' => Str::random(64),
        ]);
        $pivot = collect($data['role_ids'])->mapWithKeys(fn (int $roleId): array => [
            $roleId => ['assigned_by' => $actor->id],
        ]);
        $user->roles()->sync($pivot->all());

        return $user->load('roles');
    }
}
