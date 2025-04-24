@extends('layouts.app')

@section('content')
<div class="py-4 pt-8">
    <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-36 mx-auto mb-4">
</div>
<form method="POST" action="{{ route('register.finalization') }}" class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-4">
        Choisissez votre mot de passer pour finaliser votre inscription
    </div>

    <div class="mb-4">
        <label for="name" class="block text-gray-700 font-semibold mb-2">email</label>
        <input type="text" id="name" name="name" value="{{ $email }}" readonly
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none">
    </div>

    <div class="mb-4">
        <label for="password" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
        <input type="password" id="password" name="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#126C83] focus:outline-none">
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirmer le mot de passe</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#126C83] focus:outline-none">
    </div>

    <button type="submit"
        class="w-full bg-[#35A5A7] text-white font-semibold py-2 rounded-lg hover:bg-[#35A5A7] transition duration-300">
        Finaliser l'inscription
    </button>
</form>

@endsection
