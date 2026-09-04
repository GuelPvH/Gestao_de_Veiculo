@props([
    'title' => 'Dashboard',
    'page' => 'Dashboard',
])

<x-layouts.base :title="$title" body-class="admin-dashboard-body">
    <div class="admin-shell">
        <x-admin.sidebar />

        <div class="admin-main">
            <x-admin.topbar :page="$page ?? 'Dashboard'" />

            <main class="admin-content">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
