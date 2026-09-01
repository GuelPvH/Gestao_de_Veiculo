<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Client::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ClientType::class)],
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:32', 'unique:clients,document'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'array'],
            'address.street' => ['nullable', 'string', 'max:255'],
            'address.number' => ['nullable', 'string', 'max:30'],
            'address.city' => ['nullable', 'string', 'max:120'],
            'address.state' => ['nullable', 'string', 'size:2'],
            'address.postal_code' => ['nullable', 'string', 'max:12'],
            'status' => ['sometimes', Rule::enum(ClientStatus::class)],
        ];
    }
}
