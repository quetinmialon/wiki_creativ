@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <h1 class="text-xl font-semibold mb-4 text-center text-[#126C83]">Modifier la catégorie</h1>

    <form action="{{ route('myCategories.update', $category->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nom de la catégorie</label>
            <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('name', $category->name) }}">
            @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="role_id">Rôle associé</label>
            <select id="role_id" name="role_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @foreach($roles as $role)
                @if(!str_contains($role->name, 'Admin '))
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endif
                @endforeach
            </select>
            @error('role_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-[#35A5A7] text-white px-4 py-2 rounded hover:bg-[#126C83]">Modifier</button>
        </div>
    </form>
</div>
@endsection

