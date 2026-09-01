<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('client'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Client $client */
        $client = $this->route('client');

        return [
            'type' => ['sometimes', Rule::enum(ClientType::class)],
            'name' => ['sometimes', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:32', Rule::unique('clients')->ignore($client)],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'array'],
            'status' => ['sometimes', Rule::enum(ClientStatus::class)],
        ];
    }
}
