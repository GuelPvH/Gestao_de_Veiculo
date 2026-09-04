<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Deploy Software' }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        .code-editor { background-color: #0f172a; box-shadow: 0px 0px 20px 0px rgba(40,120,255,0.15); }
        .text-pink-400 { color: #f472b6; }
        .text-blue-300 { color: #93c5fd; }
        .text-yellow-400 { color: #facc15; }
        .text-green-300 { color: #86efac; }
        .text-orange-300 { color: #fdba74; }
    </style>
</head>
<body class="position-relative">

    {{ $slot }}

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>