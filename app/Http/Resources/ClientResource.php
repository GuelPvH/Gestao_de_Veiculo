<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Client */
final class ClientResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $canManage = $request->user()?->can('update', $this->resource) === true;

        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'name' => $this->name,
            'company_name' => $this->company_name,
            'document' => $this->when($canManage, $this->document),
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->when($canManage, $this->address),
            'status' => $this->status->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
