<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListProjects
{
    /**
     * @param array{status?: string, client_id?: int, responsible_id?: int, search?: string, per_page?: int} $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function handle(User $user, array $filters): LengthAwarePaginator
    {
        return Project::query()
            ->visibleTo($user)
            ->with(['client', 'members'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['client_id'] ?? null, fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($filters['responsible_id'] ?? null, fn ($query, $responsibleId) => $query->where('responsible_id', $responsibleId))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }
}
