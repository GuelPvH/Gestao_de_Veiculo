<?php

declare(strict_types=1);

it('renders the shared administrative shell', function (string $route): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('Menu administrativo')
        ->assertSee('Abrir menu')
        ->assertSee('Buscar')
        ->assertSee('Notificações não lidas');
})->with([
    'dashboard' => ['admin.dashboard'],
    'projects' => ['admin.projects.index'],
    'settings' => ['admin.settings.profile'],
]);

it('uses named routes for available administrative destinations', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee(route('admin.dashboard'), escape: false)
        ->assertSee(route('admin.projects.index'), escape: false)
        ->assertSee(route('admin.settings.profile'), escape: false);
});
