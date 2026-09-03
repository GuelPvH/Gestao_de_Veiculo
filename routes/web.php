<?php

declare(strict_types=1);

use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', [VehicleController::class, 'index'])->name('vehicles.index');

Route::view('/admin/dashboard', 'pages.admin.dashboard')
    ->middleware(['auth', 'can:viewPulse'])
    ->name('admin.dashboard');

Route::middleware(['auth', 'can:viewPulse'])
    ->prefix('admin/configuracoes')
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
