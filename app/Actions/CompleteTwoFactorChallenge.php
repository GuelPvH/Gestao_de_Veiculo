<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\AuthenticationLog;
use App\Models\SecurityEvent;
use App\Models\TwoFactorChallenge;
use App\Models\User;
use App\Support\Totp;

final readonly class CompleteTwoFactorChallenge
{
    public function __construct(private Totp $totp) {}

    /**
     * @return array{status: 'authenticated', user: User, token: string}|array{status: 'denied', reason: string}
     */
    public function handle(
        string $challengeToken,
        string $code,
        string $deviceName,
        ?string $ipAddress,
        ?string $userAgent,
    ): array {
        $challenge = TwoFactorChallenge::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $challengeToken))
            ->first();

        if (! $challenge instanceof TwoFactorChallenge
            || $challenge->consumed_at !== null
            || $challenge->expires_at->isPast()
            || $challenge->attempts >= 5) {
            return ['status' => 'denied', 'reason' => 'invalid_challenge'];
        }

        $user = $challenge->user;
        $recoveryCodeUsed = false;
        $valid = is_string($user->two_factor_secret)
            && $this->totp->verify($user->two_factor_secret, $code);

        if (! $valid) {
            $valid = $this->consumeRecoveryCode($user, $code);
            $recoveryCodeUsed = $valid;
        }

        if (! $valid) {
            $challenge->increment('attempts');
            $currentChallenge = $challenge->fresh();

            if ($currentChallenge instanceof TwoFactorChallenge && $currentChallenge->attempts >= 5) {
                SecurityEvent::query()->create([
                    'user_id' => $user->id,
                    'event_type' => 'two_factor_challenge_blocked',
                    'severity' => SecuritySeverity::Critical,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ]);
            }

            return ['status' => 'denied', 'reason' => 'invalid_code'];
        }

        $challenge->setAttribute('consumed_at', now());
        $challenge->save();
        $user->setAttribute('failed_login_attempts', 0);
        $user->setAttribute('locked_until', null);
        $user->setAttribute('last_login_at', now());
        $user->save();

        AuthenticationLog::query()->create([
            'user_id' => $user->id,
            'email_hash' => hash('sha256', strtolower($user->email)),
            'event' => 'login',
            'success' => true,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'metadata' => ['two_factor' => true, 'recovery_code_used' => $recoveryCodeUsed],
        ]);

        $token = $user->createToken($deviceName, ['api'], now()->addDays(7))->plainTextToken;

        return ['status' => 'authenticated', 'user' => $user, 'token' => $token];
    }

    private function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes;

        if (! is_array($codes)) {
            return false;
        }

        $hash = hash('sha256', strtoupper(trim($code)));
        $position = array_search($hash, $codes, true);

        if ($position === false) {
            return false;
        }

        unset($codes[$position]);
        $user->setAttribute('two_factor_recovery_codes', array_values($codes));
        $user->save();

        return true;
    }
}
