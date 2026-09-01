<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Project */
final class ProjectResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $canViewFinancial = $request->user()?->can('viewFinancial', $this->resource) === true;

        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'lead_id' => $this->lead_id,
            'name' => $this->name,
            'description' => $this->description,
            'project_type' => $this->project_type,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'responsible_id' => $this->responsible_id,
            'start_date' => $this->start_date?->toDateString(),
            'deadline' => $this->deadline?->toDateString(),
            'budget' => $this->when($canViewFinancial, $this->budget),
            'progress' => $this->progress,
            'technologies' => $this->technologies,
            'members' => UserResource::collection($this->whenLoaded('members')),
            'client' => new ClientResource($this->whenLoaded('client')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
