<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class SyncUserAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manageAccess', $this->route('user'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'distinct', 'exists:roles,id'],
            'permission_overrides' => ['sometimes', 'array'],
            'permission_overrides.*.permission_id' => ['required', 'integer', 'distinct', 'exists:permissions,id'],
            'permission_overrides.*.allowed' => ['required', 'boolean'],
            'permission_overrides.*.expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
