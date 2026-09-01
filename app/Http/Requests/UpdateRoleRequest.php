<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('role'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'display_name' => ['sometimes', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ];
    }
}
