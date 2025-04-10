@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-2xl font-semibold mb-4">Mon profil</h1>
        <p><strong>Nom :</strong> {{ $user->name }}</p>
        <p><strong>Email :</strong> {{ $user->email }}</p>
        <p><strong>Rôles :</strong></p>

        @if($user->roles->where('name', '!=', 'default')->isEmpty())
            Aucun rôle attribué
        @else
            <ul class="list-disc list-inside">
                @foreach($user->roles as $role)
                    @if($role->name !== 'default')
                        <li>{{ $role->name }}</li>
                    @endif
                @endforeach
            </ul>
        @endif

        <div class="mt-6 flex gap-3">
            <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Modifier</a>
            <a href="{{ route('profile.change-password') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow">Changer le mot de passe</a>
        </div>
    </div>
</div>
@endsection

