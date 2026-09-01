<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
final class TaskResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'assigned_to' => $this->assigned_to,
            'estimated_minutes' => $this->estimated_minutes,
            'due_date' => $this->due_date?->toDateString(),
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'comments' => TaskCommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
