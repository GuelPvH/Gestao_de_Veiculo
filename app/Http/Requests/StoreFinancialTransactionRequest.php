<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FinancialTransactionStatus;
use App\Enums\FinancialTransactionType;
use App\Models\FinancialTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class StoreFinancialTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', FinancialTransaction::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'category_id' => ['nullable', 'integer', 'exists:financial_categories,id'],
            'type' => ['required', Rule::enum(FinancialTransactionType::class)],
            'description' => ['required', 'string', 'max:255'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'decimal:0,2'],
            'due_date' => ['required', 'date'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::enum(FinancialTransactionStatus::class)],
            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
