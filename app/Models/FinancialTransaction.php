<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinancialTransactionStatus;
use App\Enums\FinancialTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property FinancialTransactionType $type
 * @property FinancialTransactionStatus $status
 * @property Carbon $due_date
 * @property Carbon|null $paid_at
 */
#[Fillable([
    'project_id', 'client_id', 'category_id', 'type', 'description', 'counterparty',
    'amount', 'due_date', 'paid_at', 'status', 'notes',
])]
class FinancialTransaction extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = ['status' => 'pending'];

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<FinancialCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected function casts(): array
    {
        return [
            'type' => FinancialTransactionType::class,
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'status' => FinancialTransactionStatus::class,
        ];
    }
}
