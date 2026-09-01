<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Proposal::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'lead_id' => ['nullable', 'integer', 'exists:leads,id', 'required_without:client_id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id', 'required_without:lead_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'value' => ['required', 'numeric', 'min:0.01', 'decimal:0,2'],
            'status' => ['sometimes', Rule::enum(ProposalStatus::class)],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
