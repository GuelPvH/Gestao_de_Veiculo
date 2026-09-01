<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('project'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'project_type' => ['nullable', 'string', 'max:120'],
            'status' => ['sometimes', Rule::enum(ProjectStatus::class)],
            'priority' => ['sometimes', Rule::enum(Priority::class)],
            'responsible_id' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => [Rule::prohibitedIf(! $this->user()?->hasPermission('project.view_financial')), 'nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'progress' => ['sometimes', 'integer', 'between:0,100'],
            'technologies' => ['nullable', 'array', 'max:30'],
            'technologies.*' => ['string', 'max:80'],
        ];
    }
}
