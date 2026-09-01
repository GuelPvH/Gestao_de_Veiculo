<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\AuthenticateUser;
use App\Actions\ChangePassword;
use App\Actions\CompleteTwoFactorChallenge;
use App\Actions\ConfirmTwoFactorSetup;
use App\Actions\DisableTwoFactor;
use App\Actions\StartTwoFactorSetup;
use App\Enums\SecuritySeverity;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ConfirmTwoFactorRequest;
use App\Http\Requests\DisableTwoFactorRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StartTwoFactorRequest;
use App\Http\Requests\TwoFactorChallengeRequest;
use App\Http\Resources\UserResource;
use App\Models\AuthenticationLog;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

final class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthenticateUser $authenticate): JsonResponse
    {
        $result = $authenticate->handle(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $request->string('device_name')->toString(),
            $request->ip(),
            $request->userAgent(),
        );

        if ($result['status'] === 'denied') {
            $status = $result['reason'] === 'account_locked' ? 423 : 401;

            return response()->json([
                'message' => $result['reason'] === 'email_unverified'
                    ? 'Confirme seu e-mail antes de entrar.'
                    : 'Não foi possível autenticar com as credenciais informadas.',
            ], $status);
        }

        if ($result['status'] === 'two_factor_required') {
            return response()->json([
                'data' => [
                    'requires_two_factor' => true,
                    'challenge_token' => $result['challenge_token'],
                    'expires_in' => $result['expires_in'],
                ],
            ], 202);
        }

        return response()->json([
            'data' => [
                'token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_in' => 604800,
                'user' => new UserResource($result['user']->load('roles')),
            ],
        ]);
    }

    public function twoFactorChallenge(
        TwoFactorChallengeRequest $request,
        CompleteTwoFactorChallenge $completeChallenge,
    ): JsonResponse {
        $result = $completeChallenge->handle(
            $request->string('challenge_token')->toString(),
            $request->string('code')->toString(),
            $request->string('device_name')->toString(),
            $request->ip(),
            $request->userAgent(),
        );

        if ($result['status'] === 'denied') {
            return response()->json(['message' => 'Desafio ou código inválido.'], 401);
        }

        return response()->json([
            'data' => [
                'token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_in' => 604800,
                'user' => new UserResource($result['user']->load('roles')),
            ],
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($this->user($request)->load('roles'));
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $this->user($request)->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        AuthenticationLog::query()->create([
            'user_id' => $this->user($request)->id,
            'email_hash' => hash('sha256', strtolower($this->user($request)->email)),
            'event' => 'logout',
            'success' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'Sessão encerrada.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $user = $this->user($request);
        $user->tokens()->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Todas as sessões foram encerradas.']);
    }

    public function sessions(Request $request): JsonResponse
    {
        $user = $this->user($request);

        return response()->json(['data' => [
            'tokens' => $user->tokens()
                ->latest('id')
                ->get(['id', 'name', 'last_used_at', 'expires_at', 'created_at']),
            'web_sessions' => DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get(['id', 'ip_address', 'user_agent', 'last_activity']),
        ]]);
    }

    public function revokeToken(Request $request, int $token): JsonResponse
    {
        $this->user($request)->tokens()->whereKey($token)->sole()->delete();

        return response()->json(['message' => 'Token revogado.']);
    }

    public function changePassword(ChangePasswordRequest $request, ChangePassword $changePassword): JsonResponse
    {
        $changePassword->handle(
            $this->user($request),
            $request->string('current_password')->toString(),
            $request->string('password')->toString(),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(['message' => 'Senha alterada. Entre novamente nos seus dispositivos.']);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink(['email' => $request->string('email')->toString()]);

        return response()->json([
            'message' => 'Se a conta existir, as instruções de recuperação serão enviadas.',
        ], 202);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request): void {
                $user->setAttribute('password', $password);
                $user->setAttribute('password_changed_at', now());
                $user->setAttribute('remember_token', Str::random(60));
                $user->setAttribute('failed_login_attempts', 0);
                $user->setAttribute('locked_until', null);
                $user->save();
                $user->tokens()->delete();
                event(new PasswordReset($user));

                AuthenticationLog::query()->create([
                    'user_id' => $user->id,
                    'email_hash' => hash('sha256', strtolower($user->email)),
                    'event' => 'password_reset',
                    'success' => true,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                SecurityEvent::query()->create([
                    'user_id' => $user->id,
                    'event_type' => 'password_reset',
                    'severity' => SecuritySeverity::Info,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Token de recuperação inválido ou expirado.'], 422);
        }

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }

    public function startTwoFactor(
        StartTwoFactorRequest $request,
        StartTwoFactorSetup $startSetup,
    ): JsonResponse {
        return response()->json(['data' => $startSetup->handle(
            $this->user($request),
            $request->string('current_password')->toString(),
            $request->ip(),
            $request->userAgent(),
        )]);
    }

    public function confirmTwoFactor(
        ConfirmTwoFactorRequest $request,
        ConfirmTwoFactorSetup $confirmSetup,
    ): JsonResponse {
        $codes = $confirmSetup->handle(
            $this->user($request),
            $request->string('code')->toString(),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(['data' => [
            'enabled' => true,
            'recovery_codes' => $codes,
        ]]);
    }

    public function disableTwoFactor(
        DisableTwoFactorRequest $request,
        DisableTwoFactor $disableTwoFactor,
    ): JsonResponse {
        $disableTwoFactor->handle(
            $this->user($request),
            $request->string('current_password')->toString(),
            $request->string('code')->toString(),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(['message' => 'Autenticação em dois fatores desativada.']);
    }

    private function user(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }
}
