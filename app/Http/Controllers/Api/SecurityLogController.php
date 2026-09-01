<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListAuthenticationLogsRequest;
use App\Http\Requests\ListSecurityEventsRequest;
use App\Http\Resources\AuthenticationLogResource;
use App\Http\Resources\SecurityEventResource;
use App\Models\AuthenticationLog;
use App\Models\SecurityEvent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class SecurityLogController extends Controller
{
    public function authentication(ListAuthenticationLogsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $logs = AuthenticationLog::query()
            ->with('user')
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->when(array_key_exists('success', $filters), fn ($query) => $query->where('success', $filters['success']))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 50);

        return AuthenticationLogResource::collection($logs);
    }

    public function security(ListSecurityEventsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $events = SecurityEvent::query()
            ->with('user')
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['event_type'] ?? null, fn ($query, $type) => $query->where('event_type', $type))
            ->when($filters['severity'] ?? null, fn ($query, $severity) => $query->where('severity', $severity))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 50);

        return SecurityEventResource::collection($events);
    }
}
