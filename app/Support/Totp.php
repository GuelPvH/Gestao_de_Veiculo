<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $bytes = 20): string
    {
        return $this->base32Encode(random_bytes($bytes));
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        if (preg_match('/^\d{6}$/', $code) !== 1) {
            return false;
        }

        $counter = intdiv(time(), 30);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->code($secret, $counter + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    public function uri(string $secret, string $email, string $issuer = 'Deploy'): string
    {
        $label = rawurlencode("{$issuer}:{$email}");

        return "otpauth://totp/{$label}?secret={$secret}&issuer=".rawurlencode($issuer).'&algorithm=SHA1&digits=6&period=30';
    }

    /** @return list<string> */
    public function recoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($index = 0; $index < $count; $index++) {
            $raw = strtoupper(bin2hex(random_bytes(4)));
            $codes[] = substr($raw, 0, 4).'-'.substr($raw, 4, 4);
        }

        return $codes;
    }

    private function code(string $secret, int $counter): string
    {
        $key = $this->base32Decode($secret);
        $binaryCounter = pack('N2', intdiv($counter, 4294967296), $counter % 4294967296);
        $hash = hash_hmac('sha1', $binaryCounter, $key, true);
        $offset = ord($hash[19]) & 0x0f;
        $value = unpack('N', substr($hash, $offset, 4));

        if ($value === false) {
            throw new InvalidArgumentException('Não foi possível calcular o código TOTP.');
        }

        return str_pad((string) (($value[1] & 0x7fffffff) % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $value): string
    {
        $bits = '';

        foreach (unpack('C*', $value) ?: [] as $byte) {
            $bits .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';

        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $encoded .= self::ALPHABET[bindec($chunk)];
        }

        return $encoded;
    }

    private function base32Decode(string $value): string
    {
        $value = strtoupper(str_replace([' ', '-'], '', $value));
        $bits = '';

        foreach (str_split($value) as $character) {
            $position = strpos(self::ALPHABET, $character);

            if ($position === false) {
                throw new InvalidArgumentException('Segredo TOTP inválido.');
            }

            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $decoded = '';

        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        return $decoded;
    }
}
