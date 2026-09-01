<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListClientsRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class ClientController extends Controller
{
    public function index(ListClientsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $clients = Client::query()
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);

        return ClientResource::collection($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = new Client($request->validated());
        $client->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $client->save();

        return new ClientResource($client)->response()->setStatusCode(201);
    }

    public function show(Client $client): ClientResource
    {
        Gate::authorize('view', $client);

        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client): ClientResource
    {
        $client->update($request->validated());

        return new ClientResource($client->refresh());
    }

    public function destroy(Client $client): Response
    {
        Gate::authorize('delete', $client);
        $client->delete();

        return response()->noContent();
    }
}
