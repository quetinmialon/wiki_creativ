<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion des catégories')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen bg-gray-100 font-sans leading-normal tracking-normal text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-600 p-2 w-full flex justify-between items-center fixed">
        <div>
            <a href="{{ url('/') }}" class="text-xl font-bold {{ request()->routeIs('home') ? 'text-blue-400' : 'text-gray-800' }}">
                Wiki creative
            </a>
        </div>
        <div class="text-xl font-bold flex flex-row gap-4">
            @if(Auth::check())
                <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents','documents.*') ? 'text-blue-400 font-bold' : 'text-gray-800' }}">Documents</a>
                <a href="{{ route('create-documents') }}" class="{{ request()->routeIs('create-documents') ? 'text-blue-400 font-bold' : 'text-gray-800' }}">Nouveau Document</a>
                <a href="{{ route('credentials.index') }}" class="{{ request()->routeIs('credentials','credentials.*') ? 'text-blue-400 font-bold' : 'text-gray-800' }}">Identifiants</a>
                @can('SuperAdmin', Auth::user())
                    <a href="{{ route('admin') }}" class="{{ request()->routeIs('admin','admin.*') ? 'text-blue-400 font-bold' : 'text-gray-800' }}">Admin</a>
                @endcan
                <form action="{{route('logout')}}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-800">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('subscribe') }}" class="{{ request()->routeIs('subscribe') ? 'text-blue-400 font-bold' : 'text-gray-800' }}">S'inscrire</a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'text-blue-400 font-bold' : 'text-gray-800' }}">Se connecter</a>
            @endif
        </div>
    </nav>

        <!-- Contenu principal -->
        <div class="flex-1 m-6 mt-12 p-6">
            @include('flash-messages')
            @include('errors')
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-600 text-gray-800 p-4 text-center mt-auto">
        &copy; 2025 Wiki Creative. Tous droits réservés.
    </footer>

</body>
</html>
