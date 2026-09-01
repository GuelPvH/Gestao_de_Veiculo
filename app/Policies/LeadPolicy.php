<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

final readonly class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('lead.view_any');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->hasPermission('lead.view')
            && ($user->hasPermission('lead.view_any') || $lead->assigned_to === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('lead.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->hasPermission('lead.update');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->hasPermission('lead.delete');
    }

    public function convert(User $user, Lead $lead): bool
    {
        return $user->hasPermission('lead.convert') && $lead->converted_at === null;
    }
}
