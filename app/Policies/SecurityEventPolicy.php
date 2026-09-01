<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SecurityEvent;
use App\Models\User;

final readonly class SecurityEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('security.view');
    }

    public function view(User $user, SecurityEvent $event): bool
    {
        return $user->hasPermission('security.view');
    }
}
