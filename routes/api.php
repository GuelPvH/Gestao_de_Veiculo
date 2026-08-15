<?php

declare(strict_types=1);

use App\Http\Controllers\Api\VehicleApiController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Todas as rotas já recebem o middleware `throttle:api` via configuração
| do bootstrap/app.php. Rate limit diferenciado: 120/min autenticado,
| 20/min anônimo.
|
*/

// Rotas públicas de leitura
Route::apiResource('vehicles', VehicleApiController::class)
    ->only(['index', 'show']);

// Rotas que exigem autenticação (Sanctum)
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn (Request $request) => new UserResource($request->user()))
        ->name('api.user');

    Route::apiResource('vehicles', VehicleApiController::class)
        ->only(['store', 'update', 'destroy']);
});
