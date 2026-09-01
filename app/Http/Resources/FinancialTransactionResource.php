<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin FinancialTransaction */
final class FinancialTransactionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'client_id' => $this->client_id,
            'category_id' => $this->category_id,
            'type' => $this->type->value,
            'description' => $this->description,
            'counterparty' => $this->counterparty,
            'amount' => $this->amount,
            'due_date' => $this->due_date->toDateString(),
            'paid_at' => $this->paid_at,
            'status' => $this->status->value,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
