<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Deploy') }}</title>
    
    <!-- Bootstrap 5 & Fontes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        min-height: 100vh;
        overflow-x: hidden;
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }
    
    .bg-slate-900 { background-color: #0f172a !important; }
    .bg-slate-800 { background-color: #1e293b !important; }
    .text-slate-400 { color: #94a3b8 !important; }
    .border-slate-700 { border-color: #334155 !important; }
    .bg-gray-50 { background-color: #f9fafb !important; }

    .tracking-logo-sm { letter-spacing: 9.98px; margin-right: -9.98px; }
    .tracking-logo-md { letter-spacing: 11.98px; margin-right: -11.98px; }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>