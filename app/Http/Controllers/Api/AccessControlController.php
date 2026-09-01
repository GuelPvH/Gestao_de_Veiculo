<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SyncUserAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\SyncUserAccessRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class AccessControlController extends Controller
{
    public function roles(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Role::class);

        return RoleResource::collection(Role::query()->with('permissions')->orderBy('display_name')->get());
    }

    public function permissions(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Role::class);

        return PermissionResource::collection(Permission::query()->orderBy('module')->orderBy('action')->get());
    }

    public function syncUser(
        SyncUserAccessRequest $request,
        User $user,
        SyncUserAccess $syncUserAccess,
    ): UserResource {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);
        /** @var array{role_ids: list<int>, permission_overrides?: list<array{permission_id: int, allowed: bool, expires_at?: string|null}>} $data */
        $data = $request->validated();

        return new UserResource($syncUserAccess->handle(
            $user,
            $actor,
            $data,
            $request->ip(),
            $request->userAgent(),
        ));
    }
}
