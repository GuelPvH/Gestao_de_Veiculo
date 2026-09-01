<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProposalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('proposal'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'value' => ['sometimes', 'numeric', 'min:0.01', 'decimal:0,2'],
            'status' => ['sometimes', Rule::enum(ProposalStatus::class)],
            'valid_until' => ['nullable', 'date'],
        ];
    }
}
