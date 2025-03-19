@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('login') }}" class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md">
    @csrf
    <div class="mb-4">
        <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
        <input type="email" name="email" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <div class="mb-4">
        <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
        <input type="password" name="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <button type="submit"
        class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-300">
        Login
    </button>

    <div class="mt-4 text-center">
        <a href="{{ route('password.request') }}" class="text-sm text-blue-500 hover:underline">
            Mot de passe oublié ?
        </a>
    </div>
</form>

@endsection
