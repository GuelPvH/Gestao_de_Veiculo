<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class RoleController extends Controller
{
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = new Role($request->safe()->except('permission_ids'));
        $role->setAttribute('is_system', false);
        $role->save();
        $role->permissions()->sync($request->validated('permission_ids'));

        return (new RoleResource($role->load('permissions')))->response()->setStatusCode(201);
    }

    public function show(Role $role): RoleResource
    {
        Gate::authorize('view', $role);

        return new RoleResource($role->load('permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $role->fill($request->safe()->except('permission_ids'))->save();

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->validated('permission_ids'));
        }

        return new RoleResource($role->refresh()->load('permissions'));
    }

    public function destroy(Role $role): Response
    {
        Gate::authorize('delete', $role);
        $role->delete();

        return response()->noContent();
    }
}
