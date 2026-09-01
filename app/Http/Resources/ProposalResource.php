<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Proposal */
final class ProposalResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'client_id' => $this->client_id,
            'title' => $this->title,
            'description' => $this->description,
            'value' => $this->value,
            'status' => $this->status->value,
            'valid_until' => $this->valid_until?->toDateString(),
            'accepted_at' => $this->accepted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
