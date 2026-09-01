<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    $this->seed(AccessControlSeeder::class);
});

it('cria o primeiro Super Admin por convite sem senha padrão', function (): void {
    Notification::fake();

    $this->artisan('deploy:bootstrap-admin', [
        'email' => 'primeiro-admin@example.test',
        '--name' => 'Primeiro Admin',
    ])
        ->expectsOutputToContain('convite')
        ->assertSuccessful();

    $user = User::query()->where('email', 'primeiro-admin@example.test')->sole();

    expect($user->hasRole('super_admin'))->toBeTrue()
        ->and($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('recusa o bootstrap quando já existe um Super Admin', function (): void {
    Notification::fake();
    $role = Role::query()->where('name', 'super_admin')->sole();
    $existingAdmin = User::factory()->create();
    $existingAdmin->roles()->attach($role);

    $this->artisan('deploy:bootstrap-admin', [
        'email' => 'outro-admin@example.test',
    ])
        ->expectsOutputToContain('já possui')
        ->assertFailed();

    expect(User::query()->where('email', 'outro-admin@example.test')->exists())->toBeFalse();
    Notification::assertNothingSent();
});
