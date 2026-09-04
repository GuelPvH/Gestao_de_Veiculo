<?php

declare(strict_types=1);

use App\Models\User;

it('renders the project workspace for an administrator', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(route('admin.projects.index'))
        ->assertOk()
        ->assertSee('Gerencie todos os projetos ativos e históricos')
        ->assertSee('Sistema ERP Industrial')
        ->assertSee('Dashboard Analytics BI')
        ->assertSee('class="card project-card', escape: false)
        ->assertSee('aria-current="page"', escape: false);
});

it('denies the project workspace to a non-administrator', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.projects.index'))
        ->assertForbidden();
});
