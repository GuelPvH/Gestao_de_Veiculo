<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class VehicleApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return VehicleResource::collection(Vehicle::all());
    }

    public function show(Vehicle $vehicle): VehicleResource
    {
        return new VehicleResource($vehicle);
    }

    /**
     * Validação e autorização acontecem no StoreVehicleRequest, antes deste
     * método existir. Se o corpo chegou aqui, ele é válido e permitido.
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return new VehicleResource($vehicle)
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): VehicleResource
    {
        $vehicle->update($request->validated());

        return new VehicleResource($vehicle->refresh());
    }

    /**
     * Remoção não tem corpo para validar, então não há FormRequest onde pendurar
     * a autorização — ela fica explícita aqui. `authorize` (e não `allows`)
     * porque queremos a exceção que o Laravel converte em 403.
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('delete', $vehicle);

        $vehicle->delete();

        return response()->json(['message' => 'Veículo removido com sucesso.'], 200);
    }
}
