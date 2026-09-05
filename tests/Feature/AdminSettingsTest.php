<?php

declare(strict_types=1);

it('renders every settings section', function (string $route, string $content): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('Gerencie suas preferências e configurações da conta')
        ->assertSee($content);
})->with([
    'profile' => ['admin.settings.profile', 'Informações Pessoais'],
    'company' => ['admin.settings.company', 'Dados da Empresa'],
    'notifications' => ['admin.settings.notifications', 'Canais de Notificação'],
    'security' => ['admin.settings.security', 'Alterar Senha'],
    'integrations' => ['admin.settings.integrations', 'Integrações Disponíveis'],
]);

it('marks only the selected settings tab as current', function (string $route, string $label): void {
    $response = $this->get(route($route))->assertOk();

    $response->assertSee('aria-current="page"', escape: false)
        ->assertSee('>'.$label.'</span>', escape: false);
})->with([
    'profile' => ['admin.settings.profile', 'Perfil'],
    'company' => ['admin.settings.company', 'Empresa'],
    'notifications' => ['admin.settings.notifications', 'Notificações'],
    'security' => ['admin.settings.security', 'Segurança'],
    'integrations' => ['admin.settings.integrations', 'Integrações'],
]);
