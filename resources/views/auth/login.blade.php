@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-12 col-md-7 col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <span class="admin-brand-mark d-inline-grid mb-3">D</span>
                        <h1 class="h3 mb-1">Acessar o painel</h1>
                        <p class="text-body-secondary mb-0">Entre com sua conta Deploy.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                autocomplete="username"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="current-password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input id="remember" name="remember" type="checkbox" value="1" class="form-check-input">
                            <label for="remember" class="form-check-label">Manter conectado</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
