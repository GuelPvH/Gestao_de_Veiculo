<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

final readonly class ProposalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('proposal.view_any');
    }

    public function view(User $user, Proposal $proposal): bool
    {
        return $user->hasPermission('proposal.view')
            && ($user->hasPermission('proposal.view_any') || $proposal->lead?->assigned_to === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('proposal.create');
    }

    public function update(User $user, Proposal $proposal): bool
    {
        return $user->hasPermission('proposal.update');
    }

    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->hasPermission('proposal.delete');
    }

    public function approve(User $user, Proposal $proposal): bool
    {
        return $user->hasPermission('proposal.approve');
    }
}
