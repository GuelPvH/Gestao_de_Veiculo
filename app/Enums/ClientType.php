<?php

declare(strict_types=1);

namespace App\Enums;

enum ClientType: string
{
    case Individual = 'individual';
    case Company = 'company';
}
