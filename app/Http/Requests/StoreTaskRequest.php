<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Task::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', Rule::enum(Priority::class)],
            'assigned_to' => [Rule::prohibitedIf(! $this->user()?->hasPermission('task.assign')), 'nullable', 'integer', 'exists:users,id'],
            'estimated_minutes' => ['nullable', 'integer', 'min:0', 'max:525600'],
            'due_date' => ['nullable', 'date'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }
}
