<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\AuthenticationLog;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;

final readonly class ChangePassword
{
    public function __construct(private Hasher $hasher) {}

    public function handle(
        User $user,
        string $currentPassword,
        string $newPassword,
        ?string $ipAddress,
        ?string $userAgent,
    ): void {
        if (! $this->hasher->check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'A senha atual está incorreta.']);
        }

        $user->setAttribute('password', $newPassword);
        $user->setAttribute('password_changed_at', now());
        $user->setAttribute('failed_login_attempts', 0);
        $user->setAttribute('locked_until', null);
        $user->save();
        $user->tokens()->delete();

        AuthenticationLog::query()->create([
            'user_id' => $user->id,
            'email_hash' => hash('sha256', strtolower($user->email)),
            'event' => 'password_changed',
            'success' => true,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
        SecurityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => 'password_changed',
            'severity' => SecuritySeverity::Info,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
