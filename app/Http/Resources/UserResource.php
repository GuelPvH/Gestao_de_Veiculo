<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status->value,
            'email_verified_at' => $this->email_verified_at,
            'two_factor_enabled' => $this->two_factor_confirmed_at !== null,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')->values()),
            'last_login_at' => $this->last_login_at,
        ];
    }
}
