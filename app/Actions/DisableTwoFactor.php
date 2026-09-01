<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\SecurityEvent;
use App\Models\TwoFactorChallenge;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;

final readonly class DisableTwoFactor
{
    public function __construct(
        private Hasher $hasher,
        private Totp $totp,
    ) {}

    public function handle(
        User $user,
        string $currentPassword,
        string $code,
        ?string $ipAddress,
        ?string $userAgent,
    ): void {
        $secret = $user->two_factor_secret;

        if (! $this->hasher->check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'A senha atual está incorreta.']);
        }

        if (! is_string($secret) || ! $this->totp->verify($secret, $code)) {
            throw ValidationException::withMessages(['code' => 'O código de autenticação é inválido.']);
        }

        $user->setAttribute('two_factor_secret', null);
        $user->setAttribute('two_factor_recovery_codes', null);
        $user->setAttribute('two_factor_confirmed_at', null);
        $user->save();
        TwoFactorChallenge::query()->where('user_id', $user->id)->delete();

        SecurityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => 'two_factor_disabled',
            'severity' => SecuritySeverity::Warning,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
