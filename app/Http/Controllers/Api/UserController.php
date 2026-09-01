<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateUser;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListUsersRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;

final class UserController extends Controller
{
    public function index(ListUsersRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $users = User::query()
            ->with('roles')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->whereHas('roles', fn ($query) => $query->where('roles.name', $role)))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request, CreateUser $createUser): JsonResponse
    {
        $actor = $this->actor($request);
        /** @var array{name: string, email: string, role_ids: list<int>} $data */
        $data = $request->validated();
        $user = $createUser->handle($data, $actor);
        Password::sendResetLink(['email' => $user->email]);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        Gate::authorize('view', $user);

        return new UserResource($user->load('roles'));
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $emailChanged = $request->has('email') && $request->string('email')->toString() !== $user->email;
        $user->update($request->validated());

        if ($emailChanged) {
            $user->setAttribute('email_verified_at', null);
            $user->tokens()->delete();
            $user->save();
        }

        return new UserResource($user->refresh()->load('roles'));
    }

    public function deactivate(User $user): UserResource
    {
        Gate::authorize('deactivate', $user);
        $user->setAttribute('status', UserStatus::Inactive->value);
        $user->save();
        $user->tokens()->delete();

        return new UserResource($user->load('roles'));
    }

    public function destroy(User $user): Response
    {
        Gate::authorize('delete', $user);
        $user->tokens()->delete();
        $user->delete();

        return response()->noContent();
    }

    private function actor(StoreUserRequest $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }
}
