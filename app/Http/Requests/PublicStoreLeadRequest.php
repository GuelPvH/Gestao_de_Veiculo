<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PublicStoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'desired_deadline' => ['nullable', 'date'],
            'objective' => ['required', 'string', 'max:10000'],
        ];
    }
}
