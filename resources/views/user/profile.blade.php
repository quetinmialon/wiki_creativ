@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-xl text-center font-semibold mb-4 text-[#126C83]">Informations personnelles</h1>
        <p class="text-[#126C83] pt-4">Nom :</p>
        <p>{{ $user->name }}</p>
        <p class="text-[#126C83] pt-4">Email :</p>
        <p> {{ $user->email }}</p>
        <p class="text-[#126C83] pt-4">Rôles :

        </p>

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

        <div class="mt-6 flex gap-3 justify-center">
            <a href="{{ route('profile.edit') }}" class="bg-[#35A5A7] hover:bg-[#126C83] text-white px-4 py-2 rounded shadow">Modifier ses informations personnelles</a>
            <a href="{{ route('profile.change-password') }}" class="bg-[#35A5A7] hover:bg-[#126C83] text-white px-4 py-2 rounded shadow">Changer de mot de passe</a>
        </div>
    </div>
</div>
@endsection

