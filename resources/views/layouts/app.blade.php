<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion des catégories')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <nav class="bg-blue-500 p-4">
        <div class="container mx-auto">
            <a href="{{ url('/') }}" class="text-white text-xl font-bold">Wiki creative</a>
        </div>
    </nav>
    <div class="container mx-auto py-6">
        @yield('content')
    </div>
</body>
</html>
