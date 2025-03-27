@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10 px-6">
    <h1 class="text-3xl font-bold mb-6">Créer un nouveau log</h1>
    <form action="{{ route('credentials.store') }}" method="POST" class="space-y-6 bg-white p-8 shadow-md rounded-md">
        @csrf
        <div>
            <label for="destination" class="block text-gray-700 font-medium mb-2">Destination</label>
            <input type="text" name="destination" id="destination" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
        </div>
        <div>
            <label for="username" class="block text-gray-700 font-medium mb-2">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
        </div>
        <div>
            <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
            <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
        </div>
        <div>
            <label for="role_id" class="block text-gray-700 font-medium mb-2">Partager avec un rôle</label>
            <select name="role_id" id="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                <option value="">Aucun</option>
                @foreach($roles as $role)
                @if(!str_contains($role->name, 'Admin '))
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endif
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-md shadow hover:bg-blue-600">
            Créer
        </button>
    </form>
</div>
@endsection
