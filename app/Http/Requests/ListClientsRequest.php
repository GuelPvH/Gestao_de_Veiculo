<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListClientsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Client::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::enum(ClientType::class)],
            'status' => ['nullable', Rule::enum(ClientStatus::class)],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
