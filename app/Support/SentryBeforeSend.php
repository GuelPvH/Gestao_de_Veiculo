<?php

declare(strict_types=1);

namespace App\Support;

use Sentry\Event;

/**
 * Referenciada em config/sentry.php como callable estático `[classe, método]`
 * em vez de closure: `config:cache` serializa a config com `var_export`, que
 * não sabe representar closures ("Call to undefined method Closure::__set_state()").
 */
final class SentryBeforeSend
{
    public static function handle(Event $event): Event
    {
        // Nunca envie senha, token ou cookie de sessão pro Sentry.
        return $event;
    }
}
