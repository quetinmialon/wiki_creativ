@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-2xl font-semibold mb-6">Éditer le profil</h1>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-medium mb-1">Nom</label>
                <input type="text" name="name" class="w-full border rounded px-4 py-2" value="{{ old('name', $user->name) }}">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block font-medium mb-1">Adresse e-mail</label>
                <input type="email" name="email" class="w-full border rounded px-4 py-2" value="{{ old('email', $user->email) }}">

                <p class="text-red-500 text-sm mt-1">⚠️ Modifier votre adresse e-mail changera aussi votre identifiant de connexion.</p>

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">Enregistrer</button>
                <a href="{{ route('profile.show') }}" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded shadow">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

