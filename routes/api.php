<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinancialTransactionController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectMemberController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\PublicLeadController;
use App\Http\Controllers\Api\PublicServiceController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TaskCommentController;
use App\Http\Controllers\Api\TaskController;
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
//
// `names('api.vehicles')` evita colisão com a rota web `vehicles.index`
// (o nome padrão do apiResource seria o mesmo, e `route:cache` recusa
// duas rotas com o mesmo nome).
Route::apiResource('vehicles', VehicleApiController::class)
    ->only(['index', 'show'])
    ->names('api.vehicles');

Route::get('public/services', [PublicServiceController::class, 'index'])->name('api.public.services.index');
Route::get('public/services/{service}', [PublicServiceController::class, 'show'])->name('api.public.services.show');
Route::post('public/leads', [PublicLeadController::class, 'store'])
    ->middleware('throttle:public-leads')
    ->name('api.public.leads.store');

// Rotas que exigem autenticação (Sanctum)
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn (Request $request): UserResource => new UserResource($request->user()))
        ->name('api.user');
    Route::get('/dashboard', DashboardController::class)->name('api.dashboard');

    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('api.leads.convert');
    Route::post('proposals/{proposal}/approve', [ProposalController::class, 'approve'])->name('api.proposals.approve');
    Route::post('projects/{project}/members', [ProjectMemberController::class, 'store'])->name('api.projects.members.store');
    Route::delete('projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('api.projects.members.destroy');
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('api.tasks.comments.store');
    Route::post('financial-transactions/{transaction}/approve', [FinancialTransactionController::class, 'approve'])
        ->name('api.financial-transactions.approve');

    Route::apiResources([
        'clients' => ClientController::class,
        'leads' => LeadController::class,
        'proposals' => ProposalController::class,
        'projects' => ProjectController::class,
        'tasks' => TaskController::class,
        'services' => ServiceController::class,
    ]);
    Route::apiResource('financial-transactions', FinancialTransactionController::class)
        ->parameters(['financial-transactions' => 'transaction']);
    Route::apiResource('audit-logs', AuditLogController::class)
        ->only(['index', 'show'])
        ->parameters(['audit-logs' => 'auditLog']);

    Route::apiResource('vehicles', VehicleApiController::class)
        ->only(['store', 'update', 'destroy'])
        ->names('api.vehicles');
});
