@extends('layouts.app')

@section('content')
<div class="py-4 pt-8">
    <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-36 mx-auto mb-4">
</div>
<div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md">
<h2 class="text-xl font-semibold text-[#126C83] mb-4">Réinitialiser votre mot de passe</h2>

    @if (session('status'))
        <div class="mb-4 text-green-600 bg-green-100 p-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-semibold mb-2">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#126C83] focus:outline-none">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirmez le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#126C83] focus:outline-none">
        </div>

        <button type="submit"
            class="w-full bg-[#35A5A7] text-white font-semibold py-2 rounded-lg hover:bg-[#126C83] transition duration-300">
            Réinitialiser le mot de passe
        </button>
    </form>
</div>

@endsection
