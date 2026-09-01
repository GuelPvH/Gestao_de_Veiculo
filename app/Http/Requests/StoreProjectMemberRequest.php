<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProjectMemberRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreProjectMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manageMembers', $this->route('project'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'project_role' => ['required', Rule::enum(ProjectMemberRole::class)],
        ];
    }
}
