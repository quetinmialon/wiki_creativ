<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Wiki Creative')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen font-sans leading-normal tracking-normal text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 p-2 w-full flex justify-between items-center fixed">
        <div class="border-r pr-4">
            <a href="{{ url('/') }}" class="text-xl font-bold {{ request()->routeIs('home') ? 'text-[#126C83]' : 'text-gray-800' }}">
                <img src="{{ asset('images/icone.png') }}" alt="Logo" class="h-12 inline-block mr-2">
            </a>
        </div>
        <div class=" flex flex-row gap-4">
            <div class='flex flex-row gap-4'>
            @if(Auth::check())
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Accueil</a>
                <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents','documents.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Documents</a>
                <a href="{{ route('create-documents') }}" class="{{ request()->routeIs('create-documents') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Nouveau document</a>
                <a href="{{ route('credentials.index') }}" class="{{ request()->routeIs('credentials','credentials.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Identifiants</a>
                <a href="{{ route('myCategories.myCategories') }}" class= "{{ request()->routeIs('myCategories.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Catégories</a>

                @can('SuperAdmin', Auth::user())
                    <a href="{{ route('admin') }}" class="{{ request()->routeIs('admin','admin.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Admin</a>
                @endcan
                @can('qualite', Auth::user())
                    <a href="{{ route('qualite.index') }}" class="{{ request()->routeIs('qualite','qualite.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Qualité</a>
                @endcan
            </div>

            @else
                <a href="{{ route('subscribe') }}" class="border-r-2 px-4 border-gray-200{{ request()->routeIs('subscribe') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">S'inscrire</a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Se connecter</a>
            @endif
        </div>
        @if(Auth::check())
        <div class ="border-l px-4 gap-4 border-gray-200">
            <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7] px-4">Mon Profil</a>
            <form action="{{route('logout')}}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-red-900 hover:text-red-700">Déconnexion</button>
            </form>
        </div>
        @endif
    </nav>

        <!-- Contenu principal -->
        <div class="flex-1 m-6 mt-12 p-6">
            @include('flash-messages')
            @include('errors')
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-sm bg-white border-t border-gray-200 text-gray-800 p-4 text-center mt-auto">
        &copy; 2025 Wiki Creative. Tous droits réservés.
    </footer>

</body>
</html>
