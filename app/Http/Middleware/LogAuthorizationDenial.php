<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\SecuritySeverity;
use App\Models\SecurityEvent;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class LogAuthorizationDenial
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (AuthorizationException $exception) {
            $user = $request->user();

            SecurityEvent::query()->create([
                'user_id' => $user instanceof User ? $user->id : null,
                'event_type' => 'permission_denied',
                'severity' => SecuritySeverity::Warning,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'method' => $request->method(),
                    'route' => $request->route()?->uri(),
                ],
            ]);

            throw $exception;
        }
    }
}
