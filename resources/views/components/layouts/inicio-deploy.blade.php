<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Deploy Software' }}</title>
    <!-- Bootstrap 5 CSS isolado para esta página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Estilos aplicados apenas no escopo desta página */
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        .backdrop-blur { backdrop-filter: blur(6px); background-color: rgba(255, 255, 255, 0.9); }
        .hero-section { padding-top: 10rem; padding-bottom: 6rem; }
        .code-editor { background-color: #0f172a; box-shadow: 0px 0px 20px 0px rgba(40,120,255,0.15); }
        .text-pink-400 { color: #f472b6; }
        .text-blue-300 { color: #93c5fd; }
        .text-yellow-300 { color: #fde047; }
        .text-green-300 { color: #86efac; }
    </style>
</head>
<body class="position-relative">

    {{ $slot }}

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>