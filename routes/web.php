<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', [VehicleController::class, 'index'])->name('vehicles.index');

Route::get('/solicitar-acesso', function () {
    return view('auth.request-access'); // assumindo que a view esteja em resources/views/auth/
});

Route::get('/inicio-deploy', function () {
    return view('pages.inicio-deploy');
});

Route::get('/servicos-deploy', function () {
    return view('pages.servicos-deploy');
});
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/admin/dashboard', DashboardController::class)
    ->name('admin.dashboard');

Route::get('/admin/projetos', ProjectController::class)
    ->name('admin.projects.index');

// A proteção por autenticação será reativada quando o módulo de acesso estiver pronto.
Route::prefix('admin/configuracoes')
    ->name('admin.settings.')
    ->group(function (): void {
        Route::view('/', 'pages.admin.settings.profile')->name('profile');
        Route::view('/empresa', 'pages.admin.settings.company')->name('company');
        Route::view('/notificacoes', 'pages.admin.settings.notifications')->name('notifications');
        Route::view('/seguranca', 'pages.admin.settings.security')->name('security');
        Route::view('/integracoes', 'pages.admin.settings.integrations')->name('integrations');
    });

Route::view('/publico', 'pages.publico.index')->name('publico.index');

Route::get('/up/deep', function () {
    $checks = [
        'app' => true,
        'db' => rescue(fn (): bool => (bool) DB::connection()->getPdo(), false, report: false),
        'redis' => rescue(fn (): bool => Redis::connection()->ping(), false, report: false),
    ];

    $healthy = ! in_array(false, $checks, strict: true);

    return response()->json($checks, $healthy ? 200 : 503);
})->name('health.deep');
