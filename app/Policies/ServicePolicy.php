<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

final readonly class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('service.view_any');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasPermission('service.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('service.create');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasPermission('service.update');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasPermission('service.delete');
    }

    public function publish(User $user, Service $service): bool
    {
        return $user->hasPermission('service.publish');
    }
}
