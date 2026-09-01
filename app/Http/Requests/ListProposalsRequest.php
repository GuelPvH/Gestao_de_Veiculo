<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListProposalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Proposal::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::enum(ProposalStatus::class)],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
