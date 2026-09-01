<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AuthenticationLog;
use App\Models\User;

final readonly class AuthenticationLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('security.view');
    }

    public function view(User $user, AuthenticationLog $log): bool
    {
        return $user->hasPermission('security.view');
    }
}
