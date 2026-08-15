<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plate' => ['required', 'string', 'max:10', 'unique:vehicles,plate'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'status' => ['sometimes', 'string', 'in:disponivel,em_uso,manutencao'],
        ]);

        $vehicle = Vehicle::create($validated);

        return (new VehicleResource($vehicle))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Vehicle $vehicle): VehicleResource
    {
        $validated = $request->validate([
            'plate' => ['sometimes', 'string', 'max:10', 'unique:vehicles,plate,' . $vehicle->id],
            'brand' => ['sometimes', 'string', 'max:50'],
            'model' => ['sometimes', 'string', 'max:50'],
            'year' => ['sometimes', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'status' => ['sometimes', 'string', 'in:disponivel,em_uso,manutencao'],
        ]);

        $vehicle->update($validated);

        return new VehicleResource($vehicle->refresh());
    }

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json(['message' => 'Veículo removido com sucesso.'], 200);
    }
}
