<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Lead::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'source' => ['nullable', 'string', 'max:80'],
            'project_type' => ['nullable', 'string', 'max:120'],
            'status' => ['sometimes', Rule::enum(LeadStatus::class)],
            'estimated_value' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'desired_deadline' => ['nullable', 'date'],
            'objective' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'lost_reason' => ['nullable', 'string', 'max:5000', 'required_if:status,lost'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
