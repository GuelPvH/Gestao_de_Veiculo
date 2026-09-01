<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListServicesRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class ServiceController extends Controller
{
    public function index(ListServicesRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $services = Service::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->paginate($filters['per_page'] ?? 15);

        return ServiceResource::collection($services);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = new Service($request->validated());
        $service->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $service->setAttribute('updated_by', $request->user()?->getAuthIdentifier());
        $service->save();

        return new ServiceResource($service)->response()->setStatusCode(201);
    }

    public function show(Service $service): ServiceResource
    {
        Gate::authorize('view', $service);

        return new ServiceResource($service);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $service->fill($request->validated());
        $service->setAttribute('updated_by', $request->user()?->getAuthIdentifier());
        $service->save();

        return new ServiceResource($service->refresh());
    }

    public function destroy(Service $service): Response
    {
        Gate::authorize('delete', $service);
        $service->delete();

        return response()->noContent();
    }
}
