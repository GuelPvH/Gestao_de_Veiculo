<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ConvertLeadToProject;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConvertLeadRequest;
use App\Http\Requests\ListLeadsRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Http\Resources\ProjectResource;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class LeadController extends Controller
{
    public function index(ListLeadsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $leads = Lead::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['project_type'] ?? null, fn ($query, $type) => $query->where('project_type', $type))
            ->when($filters['assigned_to'] ?? null, fn ($query, $userId) => $query->where('assigned_to', $userId))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);

        return LeadResource::collection($leads);
    }

    public function store(StoreLeadRequest $request): JsonResponse
    {
        $data = $request->safe()->except('assigned_to');
        $lead = new Lead($data);
        $lead->assigned_to = $request->validated('assigned_to');
        $lead->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $lead->save();

        return (new LeadResource($lead))->response()->setStatusCode(201);
    }

    public function show(Lead $lead): LeadResource
    {
        Gate::authorize('view', $lead);

        return new LeadResource($lead);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): LeadResource
    {
        $data = $request->safe()->except('assigned_to');
        $lead->fill($data);

        if ($request->has('assigned_to')) {
            $lead->assigned_to = $request->validated('assigned_to');
        }

        $lead->save();

        return new LeadResource($lead->refresh());
    }

    public function destroy(Lead $lead): Response
    {
        Gate::authorize('delete', $lead);
        $lead->delete();

        return response()->noContent();
    }

    public function convert(
        ConvertLeadRequest $request,
        Lead $lead,
        ConvertLeadToProject $convertLead,
    ): JsonResponse {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $project = $convertLead->handle($lead, $user, $request->validated());

        return (new ProjectResource($project))->response()->setStatusCode(201);
    }
}
