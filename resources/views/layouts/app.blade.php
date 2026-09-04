<x-layouts.base :title="trim($__env->yieldContent('title')) ?: null" body-class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('vehicles.index') }}">
                {{ config('app.name') }}
            </a>
        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

    <footer class="container py-4 text-body-secondary small">
        Laravel {{ app()->version() }} &middot; PHP {{ PHP_VERSION }} &middot; ambiente containerizado
    </footer>
</x-layouts.base>
