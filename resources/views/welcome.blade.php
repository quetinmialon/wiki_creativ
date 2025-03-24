@extends('layouts.app')
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @section('content')
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <x-favorite-list/>
    <x-last-opened-documents/>
    </body>
    @endsection
</html>
