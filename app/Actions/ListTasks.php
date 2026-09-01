<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListTasks
{
    /**
     * @param array{project_id?: int, status?: string, assigned_to?: int, search?: string, per_page?: int} $filters
     * @return LengthAwarePaginator<int, Task>
     */
    public function handle(User $user, array $filters): LengthAwarePaginator
    {
        return Task::query()
            ->visibleTo($user)
            ->with('assignee')
            ->when($filters['project_id'] ?? null, fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['assigned_to'] ?? null, fn ($query, $assignee) => $query->where('assigned_to', $assignee))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }
}
