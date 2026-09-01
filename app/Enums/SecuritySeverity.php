<?php

declare(strict_types=1);

namespace App\Enums;

enum SecuritySeverity: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';
}
