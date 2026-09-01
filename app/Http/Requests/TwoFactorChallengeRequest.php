<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class TwoFactorChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'challenge_token' => ['required', 'string', 'size:64'],
            'code' => ['required', 'string', 'regex:/^(?:\d{6}|[A-F0-9]{4}-[A-F0-9]{4})$/i'],
            'device_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\pN ._()-]+$/u'],
        ];
    }
}
