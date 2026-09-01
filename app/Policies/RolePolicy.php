<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

final readonly class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role.manage');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('role.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('role.manage');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermission('role.manage');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission('role.manage') && ! $role->is_system;
    }
}
