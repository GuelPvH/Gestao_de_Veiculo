<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ApproveProposal;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListProposalsRequest;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Http\Resources\ProposalResource;
use App\Models\Proposal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class ProposalController extends Controller
{
    public function index(ListProposalsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $proposals = Proposal::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['lead_id'] ?? null, fn ($query, $leadId) => $query->where('lead_id', $leadId))
            ->when($filters['client_id'] ?? null, fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);

        return ProposalResource::collection($proposals);
    }

    public function store(StoreProposalRequest $request): JsonResponse
    {
        $proposal = new Proposal($request->validated());
        $proposal->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $proposal->save();

        return new ProposalResource($proposal)->response()->setStatusCode(201);
    }

    public function show(Proposal $proposal): ProposalResource
    {
        Gate::authorize('view', $proposal);

        return new ProposalResource($proposal);
    }

    public function update(UpdateProposalRequest $request, Proposal $proposal): ProposalResource
    {
        $proposal->update($request->validated());

        return new ProposalResource($proposal->refresh());
    }

    public function destroy(Proposal $proposal): Response
    {
        Gate::authorize('delete', $proposal);
        $proposal->delete();

        return response()->noContent();
    }

    public function approve(Proposal $proposal, ApproveProposal $approveProposal): ProposalResource
    {
        Gate::authorize('approve', $proposal);

        return new ProposalResource($approveProposal->handle($proposal));
    }
}
