@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'bi-pie-chart-fill', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Leads/Orçamentos', 'icon' => 'bi-people-fill'],
        ['label' => 'Projetos', 'icon' => 'bi-briefcase-fill', 'route' => 'admin.projects.index', 'active' => 'admin.projects.*'],
        ['label' => 'Serviços', 'icon' => 'bi-layers-fill'],
        ['label' => 'Financeiro', 'icon' => 'bi-wallet2'],
        ['label' => 'Configurações', 'icon' => 'bi-gear-fill', 'route' => 'admin.settings.profile', 'active' => 'admin.settings.*'],
    ];
@endphp

<aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar" aria-label="Menu administrativo">
    <div class="admin-brand d-flex align-items-center gap-3">
        <span class="admin-brand-mark">D</span>
        <span class="fs-5 fw-bold">Deploy</span>
        <button type="button" class="btn-close ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Fechar menu"></button>
    </div>

    <nav class="nav nav-pills flex-column gap-1 flex-grow-1 px-3 py-2">
        @foreach ($items as $item)
            @php($isActive = isset($item['active']) && request()->routeIs($item['active']))
            <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}" class="nav-link {{ $isActive ? 'active' : '' }}" @if ($isActive) aria-current="page" @endif>
                <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="admin-user d-flex align-items-center gap-2 p-3">
        <img src="{{ asset('images/admin/admin-user.png') }}" alt="Foto do usuário administrador" class="admin-avatar rounded-circle border">
        <div class="min-w-0 flex-grow-1 lh-sm">
            <strong class="d-block text-truncate" style="font-size: 13px">Admin User</strong>
            <small class="d-block text-secondary text-truncate" style="font-size: 10px">admin@deploy.com.br</small>
        </div>
        <button type="button" class="btn btn-link p-0 text-secondary" aria-label="Sair">
            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
        </button>
    </div>
</aside>
