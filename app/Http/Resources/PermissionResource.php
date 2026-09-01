<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Permission */
final class PermissionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'module' => $this->module,
            'action' => $this->action,
            'description' => $this->description,
        ];
    }
}
