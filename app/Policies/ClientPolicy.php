<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

final readonly class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('client.view_any');
    }

    public function view(User $user, Client $client): bool
    {
        if (! $user->hasPermission('client.view')) {
            return false;
        }

        return $user->hasPermission('client.view_any')
            || $client->projects()->whereHas('members', fn ($query) => $query->where('users.id', $user->id))->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('client.create');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasPermission('client.update');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermission('client.delete');
    }
}
