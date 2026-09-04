<?php

declare(strict_types=1);

it('renders the administrative dashboard sections', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard administrativo')
        ->assertSee('Leads este mês')
        ->assertSee('Leads por Mês')
        ->assertSee('Projetos por Status')
        ->assertSee('Atividade Recente')
        ->assertSee('Últimos Leads');
});

it('marks the dashboard navigation item as current', function (): void {
    $response = $this->get(route('admin.dashboard'))->assertOk();

    $response->assertSee('href="'.route('admin.dashboard').'"', escape: false)
        ->assertSee('class="nav-link active"', escape: false)
        ->assertSee('aria-current="page"', escape: false);
});
