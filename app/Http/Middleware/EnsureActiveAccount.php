<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\SecuritySeverity;
use App\Models\SecurityEvent;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureActiveAccount
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        if ($user->isActive() && ! $user->isLocked()) {
            return $next($request);
        }

        SecurityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => 'inactive_account_access',
            'severity' => SecuritySeverity::Warning,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => ['route' => $request->path()],
        ]);

        return new JsonResponse(['message' => 'Conta indisponível.'], 403);
    }
}
