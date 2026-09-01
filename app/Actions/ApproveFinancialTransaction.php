<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\FinancialTransaction;
use App\Models\User;

final readonly class ApproveFinancialTransaction
{
    public function handle(FinancialTransaction $transaction, User $approver): FinancialTransaction
    {
        $transaction->setAttribute('approved_by', $approver->id);
        $transaction->save();

        return $transaction->refresh();
    }
}
