<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FinancialTransactionStatus;
use App\Enums\FinancialTransactionType;
use App\Models\FinancialTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class ListFinancialTransactionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', FinancialTransaction::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::enum(FinancialTransactionType::class)],
            'status' => ['nullable', Rule::enum(FinancialTransactionStatus::class)],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'category_id' => ['nullable', 'integer', 'exists:financial_categories,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }
}
