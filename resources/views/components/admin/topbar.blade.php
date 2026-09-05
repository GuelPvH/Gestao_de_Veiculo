@props(['page' => 'Dashboard'])

<header class="admin-topbar d-flex align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Abrir menu">
            <i class="bi bi-list fs-5" aria-hidden="true"></i>
        </button>
        <nav aria-label="breadcrumb" class="d-none d-sm-block">
            <ol class="breadcrumb mb-0" style="font-size: 13px">
                <li class="breadcrumb-item text-secondary">Admin</li>
                <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">{{ $page }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-3 gap-md-4">
        <label class="admin-search position-relative d-flex align-items-center">
            <span class="visually-hidden">Buscar</span>
            <i class="bi bi-search position-absolute" aria-hidden="true"></i>
            <input type="search" class="form-control form-control-sm" placeholder="Buscar..." aria-label="Buscar">
        </label>
        <button type="button" class="btn btn-link position-relative p-0 text-secondary" aria-label="Notificações não lidas">
            <i class="bi bi-bell fs-5" aria-hidden="true"></i>
            <span class="admin-notification-dot"></span>
        </button>
        <img src="{{ asset('images/admin/admin-user.png') }}" alt="Admin User" class="admin-avatar rounded-circle border">
    </div>
</header>
