<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Espace Superviseur')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <nav class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Espace Superviseur</h1>
            <div class="space-x-4">
                <a href="{{ route('supervisor.index') }}" class="text-blue-600 hover:underline">Utilisateurs</a>
                <a href="{{ route('supervisor.revokedUsers') }}" class="text-blue-600 hover:underline">Utilisateurs supprimés</a>
                <a href="{{ route('supervisor.changePassword') }}" class="text-blue-600 hover:underline">Changer mot de passe</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline bg-transparent border-none cursor-pointer">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
