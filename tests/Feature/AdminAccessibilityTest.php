<?php

declare(strict_types=1);

it('exposes page and navigation landmarks on the admin pages', function (string $route): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('<main class="admin-content">', escape: false)
        ->assertSee('aria-label="Menu administrativo"', escape: false)
        ->assertSee('aria-label="breadcrumb"', escape: false);
})->with([
    'dashboard' => ['admin.dashboard'],
    'projects' => ['admin.projects.index'],
    'settings' => ['admin.settings.profile'],
]);

it('provides accessible names for the project board and its progress bars', function (): void {
    $this->get(route('admin.projects.index'))
        ->assertOk()
        ->assertSee('aria-label="Quadro Kanban de projetos"', escape: false)
        ->assertSee('role="progressbar"', escape: false)
        ->assertSee('aria-valuemin="0"', escape: false)
        ->assertSee('aria-valuemax="100"', escape: false);
});
