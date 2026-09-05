@props(['active' => 'profile'])

@php
    $tabs = [
        'profile' => ['label' => 'Perfil', 'icon' => 'bi-person-fill', 'route' => 'admin.settings.profile'],
        'company' => ['label' => 'Empresa', 'icon' => 'bi-building-fill', 'route' => 'admin.settings.company'],
        'notifications' => ['label' => 'Notificações', 'icon' => 'bi-bell-fill', 'route' => 'admin.settings.notifications'],
        'security' => ['label' => 'Segurança', 'icon' => 'bi-shield-lock-fill', 'route' => 'admin.settings.security'],
        'integrations' => ['label' => 'Integrações', 'icon' => 'bi-plug-fill', 'route' => 'admin.settings.integrations'],
    ];
@endphp

<x-admin.layout title="Configurações" page="Configurações">
    <div class="settings-page">
        <x-ui.page-header
            class="settings-heading"
            title="Configurações"
            subtitle="Gerencie suas preferências e configurações da conta"
        />

        <nav class="settings-tabs" aria-label="Seções de configurações">
            @foreach ($tabs as $key => $tab)
                <a
                    href="{{ route($tab['route']) }}"
                    class="settings-tab {{ $active === $key ? 'active' : '' }}"
                    @if ($active === $key) aria-current="page" @endif
                >
                    <i class="bi {{ $tab['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="settings-content">
            {{ $slot }}
        </div>
    </div>
</x-admin.layout>
