@extends('layouts.app')
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @section('content')
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="max-w-2xl mx-auto mt-8 bg-white shadow-md rounded-md p-6">
            @guest
                <a href="{{ route('subscribe') }}"
                class="text-blue-500 hover:text-blue-600 font-semibold transition duration-300">
                    S'inscrire
                </a>
                <a href="{{ route('login') }}"
                class="text-green-500 hover:text-green-600 font-semibold transition duration-300">
                    Se connecter
                </a>
            @else
                <a href="{{ route('logout') }}"
                class="text-red-500 hover:text-red-600 font-semibold transition duration-300"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Se déconnecter
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endguest
    </div>
    <x-role-list/>
    <x-user-request-list/>
    </body>
    @endsection
</html>
