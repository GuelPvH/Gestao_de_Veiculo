<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Lead */
final class LeadResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => $this->source,
            'project_type' => $this->project_type,
            'status' => $this->status->value,
            'estimated_value' => $this->estimated_value,
            'desired_deadline' => $this->desired_deadline?->toDateString(),
            'objective' => $this->objective,
            'notes' => $this->notes,
            'lost_reason' => $this->lost_reason,
            'assigned_to' => $this->assigned_to,
            'client_id' => $this->client_id,
            'converted_at' => $this->converted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
