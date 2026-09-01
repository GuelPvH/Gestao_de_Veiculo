<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AccessControlController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinancialTransactionController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectMemberController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\PublicLeadController;
use App\Http\Controllers\Api\PublicServiceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SecurityLogController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TaskCommentController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
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

Route::prefix('auth')->name('api.auth.')->group(function (): void {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login');
    Route::post('two-factor/challenge', [AuthController::class, 'twoFactorChallenge'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.challenge');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:password-reset')
        ->name('password.forgot');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:password-reset')
        ->name('password.reset');
});

// Rotas que exigem autenticação (Sanctum)
Route::middleware(['auth:sanctum', 'active.account', 'security.audit'])->group(function (): void {
    Route::get('/user', fn (Request $request): UserResource => new UserResource($request->user()))
        ->name('api.user');
    Route::get('/dashboard', DashboardController::class)->name('api.dashboard');

    Route::prefix('auth')->name('api.auth.')->controller(AuthController::class)->group(function (): void {
        Route::get('me', 'me')->name('me');
        Route::post('logout', 'logout')->name('logout');
        Route::post('logout-all', 'logoutAll')->name('logout-all');
        Route::get('sessions', 'sessions')->name('sessions');
        Route::delete('tokens/{token}', 'revokeToken')->whereNumber('token')->name('tokens.destroy');
        Route::put('password', 'changePassword')->name('password.change');
        Route::post('two-factor/setup', 'startTwoFactor')->name('two-factor.setup');
        Route::post('two-factor/confirm', 'confirmTwoFactor')->name('two-factor.confirm');
        Route::delete('two-factor', 'disableTwoFactor')->name('two-factor.disable');
    });

    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('api.leads.convert');
    Route::post('proposals/{proposal}/approve', [ProposalController::class, 'approve'])->name('api.proposals.approve');
    Route::post('projects/{project}/members', [ProjectMemberController::class, 'store'])->name('api.projects.members.store');
    Route::delete('projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('api.projects.members.destroy');
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('api.tasks.comments.store');
    Route::post('financial-transactions/{transaction}/approve', [FinancialTransactionController::class, 'approve'])
        ->name('api.financial-transactions.approve');
    Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('api.users.deactivate');
    Route::put('users/{user}/access', [AccessControlController::class, 'syncUser'])->name('api.users.access.update');
    Route::get('access/roles', [AccessControlController::class, 'roles'])->name('api.access.roles');
    Route::get('access/permissions', [AccessControlController::class, 'permissions'])->name('api.access.permissions');
    Route::get('authentication-logs', [SecurityLogController::class, 'authentication'])->name('api.authentication-logs.index');
    Route::get('security-events', [SecurityLogController::class, 'security'])->name('api.security-events.index');

    Route::apiResources([
        'clients' => ClientController::class,
        'leads' => LeadController::class,
        'proposals' => ProposalController::class,
        'projects' => ProjectController::class,
        'tasks' => TaskController::class,
        'services' => ServiceController::class,
        'users' => UserController::class,
    ]);
    Route::apiResource('roles', RoleController::class)->except('index');
    Route::apiResource('financial-transactions', FinancialTransactionController::class)
        ->parameters(['financial-transactions' => 'transaction']);
    Route::apiResource('audit-logs', AuditLogController::class)
        ->only(['index', 'show'])
        ->parameters(['audit-logs' => 'auditLog']);

    Route::apiResource('vehicles', VehicleApiController::class)
        ->only(['store', 'update', 'destroy'])
        ->names('api.vehicles');
});
