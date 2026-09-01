<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ConvertLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('convert', $this->route('lead'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'integer', 'exists:clients,id', 'required_without:client'],
            'client' => ['nullable', 'array', 'required_without:client_id'],
            'client.name' => ['required_with:client', 'string', 'max:255'],
            'client.company_name' => ['nullable', 'string', 'max:255'],
            'client.document' => ['nullable', 'string', 'max:32', 'unique:clients,document'],
            'client.email' => ['nullable', 'email:rfc', 'max:255'],
            'client.phone' => ['nullable', 'string', 'max:32'],
            'project.name' => ['required', 'string', 'max:255'],
            'project.description' => ['nullable', 'string', 'max:10000'],
            'project.status' => ['sometimes', Rule::enum(ProjectStatus::class)],
            'project.priority' => ['sometimes', Rule::enum(Priority::class)],
            'project.responsible_id' => ['nullable', 'integer', 'exists:users,id'],
            'project.start_date' => ['nullable', 'date'],
            'project.deadline' => ['nullable', 'date', 'after_or_equal:project.start_date'],
            'project.budget' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'project.technologies' => ['nullable', 'array', 'max:30'],
            'project.technologies.*' => ['string', 'max:80'],
        ];
    }
}
