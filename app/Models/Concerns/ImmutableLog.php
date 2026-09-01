<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use LogicException;

trait ImmutableLog
{
    protected static function bootImmutableLog(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Registros de log são imutáveis.');
        });
        static::deleting(static function (): never {
            throw new LogicException('Registros de log são imutáveis.');
        });
    }
}
