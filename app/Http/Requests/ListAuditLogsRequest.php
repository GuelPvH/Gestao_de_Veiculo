<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\AuditLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class ListAuditLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', AuditLog::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:50'],
            'entity_type' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
