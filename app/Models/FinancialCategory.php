<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinancialTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'is_active'])]
class FinancialCategory extends Model
{
    /** @return HasMany<FinancialTransaction, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }

    protected function casts(): array
    {
        return [
            'type' => FinancialTransactionType::class,
            'is_active' => 'boolean',
        ];
    }
}
