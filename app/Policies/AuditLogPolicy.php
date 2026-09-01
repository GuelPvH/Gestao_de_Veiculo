<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

final readonly class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('audit.view');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasPermission('audit.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}
