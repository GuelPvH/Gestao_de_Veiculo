@props([
    'title' => null,
    'bodyClass' => null,
    'theme' => 'light',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' | '.config('app.name', 'Deploy') : config('app.name', 'Deploy') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    @isset($head)
        {{ $head }}
    @endisset
</head>
<body {{ $attributes->class([$bodyClass]) }}>
    {{ $slot }}

    @isset($scripts)
        {{ $scripts }}
    @endisset
</body>
</html>
