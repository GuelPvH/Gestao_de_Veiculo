<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ListVehicles;
use App\Enums\VehicleStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class VehicleController extends Controller
{
    public function index(Request $request, ListVehicles $listVehicles): View
    {
        $status = VehicleStatus::tryFrom($request->string('status')->toString());

        return view('vehicles.index', [
            'vehicles' => $listVehicles->handle($status),
            'statuses' => VehicleStatus::cases(),
            'selectedStatus' => $status,
        ]);
    }
}
