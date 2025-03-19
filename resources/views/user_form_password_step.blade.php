@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('register.finalization') }}" class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-4">
        <label for="password" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
        <input type="password" id="password" name="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <button type="submit"
        class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-300">
        Finaliser l'inscription
    </button>
</form>

@endsection
