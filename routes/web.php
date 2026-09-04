<?php

declare(strict_types=1);

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

Route::get('/up/deep', function () {
    $checks = [
        'app' => true,
        'db' => rescue(fn (): bool => (bool) DB::connection()->getPdo(), false, report: false),
        'redis' => rescue(fn (): bool => Redis::connection()->ping(), false, report: false),
    ];

    $healthy = ! in_array(false, $checks, strict: true);

    return response()->json($checks, $healthy ? 200 : 503);
})->name('health.deep');
