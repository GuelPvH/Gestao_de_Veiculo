<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AuthenticationLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AuthenticationLog */
final class AuthenticationLogResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'event' => $this->event,
            'success' => $this->success,
            'failure_reason' => $this->failure_reason,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
