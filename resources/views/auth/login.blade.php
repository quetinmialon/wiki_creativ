@extends('layouts.app')

@section('title', 'Se Connecter')

@section('content')
<div class="py-4 pt-8">
    <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-36 mx-auto mb-4">
</div>
<form method="POST" action="{{ route('login') }}" class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md">
    @csrf
    <div class="mb-4">
        <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
        <input type="email" name="email" required id="email"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#35A5A7] focus:outline-none">
    </div>

    <div class="mb-4">
        <label for="password" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
        <input type="password" name="password" required id="password"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#35A5A7] focus:outline-none">
    </div>

    <button type="submit"
        class="w-full bg-[#35A5A7] text-white font-semibold py-2 rounded-lg hover:bg-[#126C83] transition duration-300">
        Se connecter
    </button>

    <div class="mt-4 text-center">
        <a href="{{ route('password.request') }}" class="text-sm text-[#126C83] hover:underline">
            Mot de passe oublié ?
        </a>
    </div>
</form>

@endsection
