<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion des catégories')</title>
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
                <a href="{{ route('subscribe') }}" class="{{ request()->routeIs('subscribe') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">S'inscrire</a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">Se connecter</a>
            @endif
        </div>
        <div class ="border-l px-4 gap-4 border-gray-200">
            <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7] px-4">Mon Profil</a>
            <form action="{{route('logout')}}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-red-900 hover:text-red-700">Déconnexion</button>
            </form>
        </div>
    </nav>

    <!-- Conteneur principal avec sidebar -->
    <div class="flex flex-1">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r border-b border-t border-gray-200 p-6 space-y-6 fixed top-16 bottom-16 left-0">
            <a href="{{ route('admin') }}"><div class="text-xl font-bold text-[#126C83] hover:text-[#35A5A7]">Admin Panel</div></a>
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('admin.documents.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.documents.*','admin.documents') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Documents
                    </a>
                </li>
                <li>
                    <a href="{{ route('everyLogs') }}" class="block px-4 py-2 rounded {{ request()->routeIs('logs','logs.*','everyLogs') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('categories.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Catégories
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.users') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Utilisateurs
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.roles') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Rôles
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users-requests') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.users-requests') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Demandes d'inscriptions
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.permissions') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.permissions', 'admin.permissions.*') ? 'text-[#126C83]' : 'text-gray-800' }} hover:text-[#35A5A7]">
                         Accès temporaires aux documents
                    </a>
                </li>
            </ul>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 ml-64 mt-12 p-6">
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
