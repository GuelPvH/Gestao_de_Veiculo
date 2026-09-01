<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\BuildDashboard;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    public function __invoke(Request $request, BuildDashboard $buildDashboard): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        abort_unless(
            $user->hasPermission('dashboard.view_general')
            || $user->hasPermission('dashboard.view_personal')
            || $user->hasPermission('dashboard.view_financial'),
            403,
        );

        return response()->json(['data' => $buildDashboard->handle($user)]);
    }
}
