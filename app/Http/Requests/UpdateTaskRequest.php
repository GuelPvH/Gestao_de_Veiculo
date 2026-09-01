<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('task'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $fullUpdate = $this->user()?->hasPermission('task.update') === true;

        return [
            'project_id' => [Rule::prohibitedIf(! $fullUpdate), 'sometimes', 'integer', 'exists:projects,id'],
            'title' => [Rule::prohibitedIf(! $fullUpdate), 'sometimes', 'string', 'max:255'],
            'description' => [Rule::prohibitedIf(! $fullUpdate), 'nullable', 'string', 'max:20000'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority' => [Rule::prohibitedIf(! $fullUpdate), 'sometimes', Rule::enum(Priority::class)],
            'assigned_to' => [Rule::prohibitedIf(! $this->user()?->hasPermission('task.assign')), 'nullable', 'integer', 'exists:users,id'],
            'estimated_minutes' => [Rule::prohibitedIf(! $fullUpdate), 'nullable', 'integer', 'min:0', 'max:525600'],
            'due_date' => [Rule::prohibitedIf(! $fullUpdate), 'nullable', 'date'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }
}
