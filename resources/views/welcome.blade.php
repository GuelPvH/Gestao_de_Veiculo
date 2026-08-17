<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Veículos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container py-5">
        <header class="pb-3 mb-4 border-bottom d-flex justify-content-between align-items-center">
            <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                <span class="fs-4">🚗 Gestão de Veículos</span>
            </a>
            @if (Route::has('login'))
                <nav>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
            <div class="container-fluid py-5 text-center">
                <h1 class="display-5 fw-bold text-primary mb-3">Bem-vindo ao Sistema</h1>
                <p class="col-md-8 mx-auto fs-5 text-muted">
                    O seu ambiente está configurado! O <strong>Bootstrap</strong> está funcionando perfeitamente via Vite. Agora você pode começar a desenvolver o seu projeto da faculdade sem se preocupar com conflitos de CSS.
                </p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button class="btn btn-primary btn-lg px-4" type="button">Começar a desenvolver</button>
                </div>
            </div>
        </div>

        <footer class="pt-3 mt-4 text-muted border-top text-center">
            &copy; {{ date('Y') }} - Projeto da Faculdade
        </footer>
    </div>
</body>
</html>
