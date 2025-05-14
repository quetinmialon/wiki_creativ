@extends('layouts.admin')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-md rounded-md p-6 mt-10">
    <h2 class="text-xl font-semibold text-center text-[#126C83] mb-4">Modifier le Rôle</h2>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Nom du Rôle :</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $role->name) }}"
                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-2"
                placeholder="Entrez le nom du rôle"
                required
            >
        </div>
        <div class="flex flex-row justify-center">
            <button
                type="submit"
                class="bg-[#35A5A7] text-white font-bold py-2 px-4 rounded-md hover:bg-[#126C83] transition duration-200">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
