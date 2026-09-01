<?php

declare(strict_types=1);

namespace App\Enums;

enum FinancialTransactionStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';
}
