<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\AuthenticationLog;
use App\Models\SecurityEvent;
use App\Models\TwoFactorChallenge;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

final readonly class AuthenticateUser
{
    private const DUMMY_HASH = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    private const MAX_ATTEMPTS = 5;

    private const LOCK_MINUTES = 15;

    public function __construct(private Hasher $hasher) {}

    /**
     * @return array{status: 'authenticated', user: User, token: string}|array{status: 'two_factor_required', challenge_token: string, expires_in: int}|array{status: 'denied', reason: string}
     */
    public function handle(
        string $email,
        string $password,
        string $deviceName,
        ?string $ipAddress,
        ?string $userAgent,
    ): array {
        $normalizedEmail = Str::lower(trim($email));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();
        $passwordMatches = $this->hasher->check($password, $user?->password ?? self::DUMMY_HASH);

        if (! $user instanceof User || ! $passwordMatches) {
            $this->failedAttempt($user, $normalizedEmail, $ipAddress, $userAgent);

            return ['status' => 'denied', 'reason' => 'invalid_credentials'];
        }

        if (! $user->isActive()) {
            $this->authenticationLog($user, $normalizedEmail, 'login_failed', false, 'inactive_account', $ipAddress, $userAgent);

            return ['status' => 'denied', 'reason' => 'account_unavailable'];
        }

        if ($user->isLocked()) {
            $this->authenticationLog($user, $normalizedEmail, 'login_failed', false, 'account_locked', $ipAddress, $userAgent);

            return ['status' => 'denied', 'reason' => 'account_locked'];
        }

        if ($user->email_verified_at === null) {
            $this->authenticationLog($user, $normalizedEmail, 'login_failed', false, 'email_unverified', $ipAddress, $userAgent);

            return ['status' => 'denied', 'reason' => 'email_unverified'];
        }

        if ($user->two_factor_confirmed_at !== null && $user->two_factor_secret !== null) {
            $plainToken = Str::random(64);
            TwoFactorChallenge::query()->create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $plainToken),
                'expires_at' => now()->addMinutes(5),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
            $this->authenticationLog($user, $normalizedEmail, 'two_factor_challenge', true, null, $ipAddress, $userAgent);

            return [
                'status' => 'two_factor_required',
                'challenge_token' => $plainToken,
                'expires_in' => 300,
            ];
        }

        $token = $this->completeLogin($user, $deviceName, $normalizedEmail, $ipAddress, $userAgent);

        return ['status' => 'authenticated', 'user' => $user, 'token' => $token];
    }

    private function failedAttempt(?User $user, string $email, ?string $ipAddress, ?string $userAgent): void
    {
        if ($user instanceof User) {
            $attempts = $user->failed_login_attempts + 1;
            $user->setAttribute('failed_login_attempts', $attempts);

            if ($attempts >= self::MAX_ATTEMPTS) {
                $user->setAttribute('locked_until', now()->addMinutes(self::LOCK_MINUTES));
                SecurityEvent::query()->create([
                    'user_id' => $user->id,
                    'event_type' => 'account_locked',
                    'severity' => SecuritySeverity::Critical,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'metadata' => ['failed_attempts' => $attempts],
                ]);
            } elseif ($attempts >= 3) {
                SecurityEvent::query()->create([
                    'user_id' => $user->id,
                    'event_type' => 'multiple_login_failures',
                    'severity' => SecuritySeverity::Warning,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'metadata' => ['failed_attempts' => $attempts],
                ]);
            }

            $user->save();
        }

        $this->authenticationLog($user, $email, 'login_failed', false, 'invalid_credentials', $ipAddress, $userAgent);
    }

    private function completeLogin(
        User $user,
        string $deviceName,
        string $email,
        ?string $ipAddress,
        ?string $userAgent,
    ): string {
        $user->setAttribute('failed_login_attempts', 0);
        $user->setAttribute('locked_until', null);
        $user->setAttribute('last_login_at', now());
        $user->save();

        $this->authenticationLog($user, $email, 'login', true, null, $ipAddress, $userAgent);

        return $user->createToken($deviceName, ['api'], now()->addDays(7))->plainTextToken;
    }

    private function authenticationLog(
        ?User $user,
        string $email,
        string $event,
        bool $success,
        ?string $failureReason,
        ?string $ipAddress,
        ?string $userAgent,
    ): void {
        AuthenticationLog::query()->create([
            'user_id' => $user?->id,
            'email_hash' => hash('sha256', $email),
            'event' => $event,
            'success' => $success,
            'failure_reason' => $failureReason,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
