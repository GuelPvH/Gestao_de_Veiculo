<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Task::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'status' => ['nullable', Rule::enum(TaskStatus::class)],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
