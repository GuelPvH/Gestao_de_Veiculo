<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;

final readonly class StartTwoFactorSetup
{
    public function __construct(
        private Hasher $hasher,
        private Totp $totp,
    ) {}

    /** @return array{secret: string, otpauth_uri: string} */
    public function handle(User $user, string $currentPassword, ?string $ipAddress, ?string $userAgent): array
    {
        if (! $this->hasher->check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'A senha atual está incorreta.']);
        }

        $secret = $this->totp->generateSecret();
        $user->setAttribute('two_factor_secret', $secret);
        $user->setAttribute('two_factor_recovery_codes', null);
        $user->setAttribute('two_factor_confirmed_at', null);
        $user->save();

        SecurityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => 'two_factor_setup_started',
            'severity' => SecuritySeverity::Info,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return [
            'secret' => $secret,
            'otpauth_uri' => $this->totp->uri($secret, $user->email),
        ];
    }
}
