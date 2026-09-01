<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('user'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email:rfc', 'max:255', Rule::unique('users')->ignore($user)],
            'avatar_path' => ['nullable', 'string', 'max:255'],
        ];
    }
}
