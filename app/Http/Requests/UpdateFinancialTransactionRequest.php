<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FinancialTransactionStatus;
use App\Enums\FinancialTransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class UpdateFinancialTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('transaction'));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'category_id' => ['nullable', 'integer', 'exists:financial_categories,id'],
            'type' => ['sometimes', Rule::enum(FinancialTransactionType::class)],
            'description' => ['sometimes', 'string', 'max:255'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'decimal:0,2'],
            'due_date' => ['sometimes', 'date'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::enum(FinancialTransactionStatus::class)],
            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
