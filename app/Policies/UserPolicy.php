<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final readonly class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('user.view_any');
    }

    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->hasPermission('user.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('user.create');
    }

    public function update(User $user, User $target): bool
    {
        return ($user->id === $target->id || $user->hasPermission('user.update'))
            && (! $target->isSuperAdmin() || $user->isSuperAdmin());
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->id !== $target->id
            && ! $target->isSuperAdmin()
            && $user->hasPermission('user.deactivate');
    }

    public function delete(User $user, User $target): bool
    {
        return $user->id !== $target->id
            && ! $target->isSuperAdmin()
            && $user->hasPermission('user.delete');
    }

    public function manageAccess(User $user, User $target): bool
    {
        return $user->id !== $target->id && $user->hasPermission('role.manage');
    }
}
