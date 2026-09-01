<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListProjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Project::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(ProjectStatus::class)],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'responsible_id' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
