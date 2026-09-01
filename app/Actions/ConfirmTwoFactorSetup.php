<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SecuritySeverity;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Validation\ValidationException;

final readonly class ConfirmTwoFactorSetup
{
    public function __construct(private Totp $totp) {}

    /** @return list<string> */
    public function handle(User $user, string $code, ?string $ipAddress, ?string $userAgent): array
    {
        $secret = $user->two_factor_secret;

        if (! is_string($secret) || ! $this->totp->verify($secret, $code)) {
            throw ValidationException::withMessages(['code' => 'O código de autenticação é inválido.']);
        }

        $recoveryCodes = $this->totp->recoveryCodes();
        $user->setAttribute('two_factor_recovery_codes', array_map(
            fn (string $recoveryCode): string => hash('sha256', $recoveryCode),
            $recoveryCodes,
        ));
        $user->setAttribute('two_factor_confirmed_at', now());
        $user->save();

        SecurityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => 'two_factor_enabled',
            'severity' => SecuritySeverity::Info,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return $recoveryCodes;
    }
}
