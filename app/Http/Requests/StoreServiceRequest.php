<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Service::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', 'unique:services,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'features' => ['nullable', 'array', 'max:50'],
            'features.*' => ['string', 'max:255'],
            'tags' => ['nullable', 'array', 'max:30'],
            'tags.*' => ['string', 'max:80'],
            'base_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'status' => ['sometimes', Rule::enum(ServiceStatus::class)],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
