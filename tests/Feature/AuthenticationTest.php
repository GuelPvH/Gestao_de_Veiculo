<?php

declare(strict_types=1);

use App\Models\User;

it('allows temporary unauthenticated access to the admin dashboard', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard administrativo')
        ->assertSee('Sem autenticação');
});

it('renders the login screen', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Acessar o painel');
});

it('authenticates a user and redirects to the intended admin page', function (): void {
    $admin = User::factory()->create([
        'email' => 'admin@example.test',
        'password' => 'secret-password',
        'is_admin' => true,
    ]);

    $this->get(route('admin.dashboard'));

    $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('rejects invalid credentials', function (): void {
    $user = User::factory()->create();

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs out an authenticated user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('logout'))
        ->assertRedirectToRoute('login');

    $this->assertGuest();
});
