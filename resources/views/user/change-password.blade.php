@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-xl font-semibold text-center text-[#126C83] mb-6">Changer de mot de passe</h1>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="current_password" class="block font-medium mb-1">Mot de passe actuel</label>
                <input type="password" name="current_password" class="w-full border rounded px-4 py-2">
                @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="new_password" class="block font-medium mb-1">Nouveau mot de passe</label>
                <input type="password" name="new_password" class="w-full border rounded px-4 py-2">
                @error('new_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="new_password_confirmation" class="block font-medium mb-1">Confirmation</label>
                <input type="password" name="new_password_confirmation" class="w-full border rounded px-4 py-2">
            </div>

            <div class="flex gap-3 justify-center">
                <button type="submit" class="bg-[#35A5A7] hover:bg-[#126C83] text-white px-4 py-2 rounded shadow">Confirmer</button>
                <a href="{{ route('profile.show') }}" class="bg-white hover:bg-[#126C83] hover:text-white text-[#126C83] px-4 py-2 rounded shadow">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

