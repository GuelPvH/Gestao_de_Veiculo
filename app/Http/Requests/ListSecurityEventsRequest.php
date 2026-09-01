<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\SecuritySeverity;
use App\Models\SecurityEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListSecurityEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', SecurityEvent::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'event_type' => ['nullable', 'string', 'max:80'],
            'severity' => ['nullable', Rule::enum(SecuritySeverity::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
