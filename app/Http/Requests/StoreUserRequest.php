<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', User::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $roleExists = Rule::exists('roles', 'id');

        if (! $this->user()?->hasPermission('role.manage')) {
            $roleExists->whereIn('name', ['product_owner', 'developer']);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'distinct', $roleExists],
        ];
    }
}
