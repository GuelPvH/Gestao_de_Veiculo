<?php

declare(strict_types=1);

use App\Enums\UserStatus;
use App\Models\AuthenticationLog;
use App\Models\Role;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Support\Totp;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function (): void {
    $this->seed(AccessControlSeeder::class);
});

it('autentica conta ativa e registra o acesso sem armazenar credenciais', function (): void {
    $user = User::factory()->create(['password' => 'Senha#Segura123']);

    postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'Senha#Segura123',
        'device_name' => 'Firefox Ubuntu',
    ])
        ->assertOk()
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonMissingPath('data.user.password');

    $log = AuthenticationLog::query()->where('event', 'login')->sole();
    expect($log->success)->toBeTrue()
        ->and($log->email_hash)->toBe(hash('sha256', strtolower($user->email)))
        ->and($log->getAttributes())->not->toHaveKeys(['password', 'token']);
});

it('bloqueia temporariamente a conta após cinco senhas incorretas', function (): void {
    $user = User::factory()->create(['password' => 'Senha#Segura123']);

    foreach (range(1, 5) as $attempt) {
        postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => "SenhaErrada#{$attempt}",
            'device_name' => 'Teste automatizado',
        ])->assertUnauthorized();
    }

    $user->refresh();
    expect($user->failed_login_attempts)->toBe(5)
        ->and($user->isLocked())->toBeTrue()
        ->and(SecurityEvent::query()->where('event_type', 'account_locked')->exists())->toBeTrue();
});

it('nega tokens de contas inativas em todas as rotas protegidas', function (): void {
    $user = User::factory()->create();
    $user->setAttribute('status', UserStatus::Inactive->value);
    $user->save();
    Sanctum::actingAs($user);

    getJson('/api/auth/me')->assertForbidden();

    expect(SecurityEvent::query()->where('event_type', 'inactive_account_access')->exists())->toBeTrue();
});

it('registra tentativa de acesso sem permissão como evento de segurança', function (): void {
    Sanctum::actingAs(authUserWithRole('developer'));

    getJson('/api/financial-transactions')->assertForbidden();

    expect(SecurityEvent::query()->where('event_type', 'permission_denied')->exists())->toBeTrue();
});

it('habilita TOTP e exige o segundo fator antes de emitir o token', function (): void {
    $user = User::factory()->create(['password' => 'Senha#Segura123']);
    Sanctum::actingAs($user);
    /** @var Totp $totp */
    $totp = app(Totp::class);

    $setup = postJson('/api/auth/two-factor/setup', [
        'current_password' => 'Senha#Segura123',
    ])->assertOk();
    $secret = $setup->json('data.secret');
    expect($secret)->toBeString();

    postJson('/api/auth/two-factor/confirm', [
        'code' => $totp->codeAt($secret),
    ])
        ->assertOk()
        ->assertJsonCount(8, 'data.recovery_codes');

    $login = postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'Senha#Segura123',
        'device_name' => 'Firefox Ubuntu',
    ])->assertStatus(202)->assertJsonPath('data.requires_two_factor', true);

    postJson('/api/auth/two-factor/challenge', [
        'challenge_token' => $login->json('data.challenge_token'),
        'code' => $totp->codeAt($secret),
        'device_name' => 'Firefox Ubuntu',
    ])
        ->assertOk()
        ->assertJsonPath('data.token_type', 'Bearer');
});

it('revoga todos os tokens quando a senha é alterada', function (): void {
    $user = User::factory()->create(['password' => 'Senha#Antiga123']);
    $user->createToken('dispositivo antigo');
    Sanctum::actingAs($user);

    putJson('/api/auth/password', [
        'current_password' => 'Senha#Antiga123',
        'password' => 'Senha#Nova45678',
        'password_confirmation' => 'Senha#Nova45678',
    ])->assertOk();

    expect($user->tokens()->count())->toBe(0)
        ->and(Hash::check('Senha#Nova45678', $user->refresh()->password))->toBeTrue();
});

it('não revela se um email existe ao solicitar recuperação', function (): void {
    postJson('/api/auth/forgot-password', [
        'email' => 'nao-existe@example.test',
    ])
        ->assertStatus(202)
        ->assertJsonPath('message', 'Se a conta existir, as instruções de recuperação serão enviadas.');
});

it('impede gestor de atribuir perfil privilegiado ao criar usuário', function (): void {
    $manager = authUserWithRole('manager');
    $superAdminRole = Role::query()->where('name', 'super_admin')->sole();
    Sanctum::actingAs($manager);

    postJson('/api/users', [
        'name' => 'Novo Admin',
        'email' => 'novo-admin@example.test',
        'role_ids' => [$superAdminRole->id],
    ])->assertUnprocessable()->assertJsonValidationErrors('role_ids.0');
});

it('permite ao Super Admin criar convite sem devolver senha temporária', function (): void {
    $admin = authUserWithRole('super_admin');
    $developerRole = Role::query()->where('name', 'developer')->sole();
    Sanctum::actingAs($admin);

    postJson('/api/users', [
        'name' => 'Nova Pessoa',
        'email' => 'nova-pessoa@example.test',
        'role_ids' => [$developerRole->id],
    ])
        ->assertCreated()
        ->assertJsonPath('data.roles.0', 'developer')
        ->assertJsonMissingPath('data.password');
});

function authUserWithRole(string $roleName): User
{
    $user = User::factory()->create();
    $role = Role::query()->where('name', $roleName)->sole();
    $user->roles()->attach($role->id, [
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $user;
}
