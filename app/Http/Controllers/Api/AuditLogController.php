<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListAuditLogsRequest;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class AuditLogController extends Controller
{
    public function index(ListAuditLogsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $logs = AuditLog::query()
            ->with('user')
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['entity_type'] ?? null, fn ($query, $type) => $query->where('auditable_type', $type))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 50);

        return AuditLogResource::collection($logs);
    }

    public function show(AuditLog $auditLog): AuditLogResource
    {
        Gate::authorize('view', $auditLog);

        return new AuditLogResource($auditLog->load('user'));
    }
}
