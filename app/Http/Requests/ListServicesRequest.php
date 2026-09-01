<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListServicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Service::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(ServiceStatus::class)],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
