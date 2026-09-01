<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

final readonly class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('task.view_all')
            || $user->hasPermission('task.view_assigned');
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->hasPermission('task.view_all')) {
            return true;
        }

        return $user->hasPermission('task.view_assigned')
            && $task->project->members()
                ->where('users.id', $user->id)
                ->whereNull('project_members.left_at')
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('task.create');
    }

    public function update(User $user, Task $task): bool
    {
        return $user->hasPermission('task.update')
            || ($user->hasPermission('task.update_status') && $task->assigned_to === $user->id);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasPermission('task.delete');
    }

    public function comment(User $user, Task $task): bool
    {
        return $user->hasPermission('task.comment') && $this->view($user, $task);
    }
}
