<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\Role;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Validation\ValidationException;

final readonly class SyncUserAccess
{
    public function __construct(private ConnectionInterface $connection) {}

    /**
     * @param array{role_ids: list<int>, permission_overrides?: list<array{permission_id: int, allowed: bool, expires_at?: string|null}>} $data
     */
    public function handle(User $target, User $actor, array $data, ?string $ipAddress, ?string $userAgent): User
    {
        return $this->connection->transaction(function () use ($target, $actor, $data, $ipAddress, $userAgent): User {
            $superAdmin = Role::query()->where('name', 'super_admin')->sole();

            if ($target->hasRole('super_admin')
                && ! in_array($superAdmin->id, $data['role_ids'], true)
                && User::query()->whereHas('roles', fn ($query) => $query->where('roles.name', 'super_admin'))->count() <= 1) {
                throw ValidationException::withMessages([
                    'role_ids' => 'O sistema precisa manter ao menos um Super Admin.',
                ]);
            }

            $previousRoles = $target->roles()->pluck('roles.id')->all();
            $rolePivot = collect($data['role_ids'])->mapWithKeys(fn (int $roleId): array => [
                $roleId => ['assigned_by' => $actor->id],
            ]);
            $target->roles()->sync($rolePivot->all());

            $permissionPivot = collect($data['permission_overrides'] ?? [])->mapWithKeys(
                fn (array $override): array => [
                    $override['permission_id'] => [
                        'allowed' => $override['allowed'],
                        'assigned_by' => $actor->id,
                        'expires_at' => $override['expires_at'] ?? null,
                    ],
                ],
            );
            $target->directPermissions()->sync($permissionPivot->all());

            SecurityEvent::query()->create([
                'user_id' => $actor->id,
                'event_type' => 'user_access_changed',
                'severity' => SecuritySeverity::Warning,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'metadata' => [
                    'target_user_id' => $target->id,
                    'previous_role_ids' => $previousRoles,
                    'new_role_ids' => $data['role_ids'],
                    'permission_override_ids' => $permissionPivot->keys()->all(),
                ],
            ]);

            $target->tokens()->delete();

            return $target->load(['roles', 'directPermissions']);
        });
    }
}
