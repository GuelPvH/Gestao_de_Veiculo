<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

final readonly class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('project.view_all')
            || $user->hasPermission('project.view_assigned');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasPermission('project.view_all')) {
            return true;
        }

        return $user->hasPermission('project.view_assigned')
            && $project->members()
                ->where('users.id', $user->id)
                ->whereNull('project_members.left_at')
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('project.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermission('project.update');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermission('project.delete');
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $user->hasPermission('project.manage_members');
    }

    public function viewFinancial(User $user, Project $project): bool
    {
        return $user->hasPermission('project.view_financial');
    }
}
