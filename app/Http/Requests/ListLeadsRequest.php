<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListLeadsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Lead::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(LeadStatus::class)],
            'project_type' => ['nullable', 'string', 'max:120'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
