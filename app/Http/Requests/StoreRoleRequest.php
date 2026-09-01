<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Role::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'regex:/^[a-z][a-z0-9_]*$/', 'unique:roles,name'],
            'display_name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ];
    }
}
