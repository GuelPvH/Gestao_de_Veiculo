<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FinancialTransaction;
use App\Models\User;

final readonly class FinancialTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('finance.view_any');
    }

    public function view(User $user, FinancialTransaction $transaction): bool
    {
        return $user->hasPermission('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('finance.create');
    }

    public function update(User $user, FinancialTransaction $transaction): bool
    {
        return $user->hasPermission('finance.update');
    }

    public function delete(User $user, FinancialTransaction $transaction): bool
    {
        return $user->hasPermission('finance.delete');
    }

    public function approve(User $user, FinancialTransaction $transaction): bool
    {
        return $user->hasPermission('finance.approve');
    }
}
