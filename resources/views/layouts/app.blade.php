<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">

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

</body>
</html>
