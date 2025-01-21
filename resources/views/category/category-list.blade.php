@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Liste des Catégories</h1>
        <a href="{{ route('categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ajouter une catégorie</a>
    </div>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-4 py-2">#</th>
                <th class="border border-gray-300 px-4 py-2">Nom</th>
                <th class="border border-gray-300 px-4 py-2">Rôle Associé</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr class="text-center">
                    <td class="border border-gray-300 px-4 py-2">{{ $category->id }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $category->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $category->role->name ?? 'N/A' }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="{{ route('categories.edit', $category->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Modifier</a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">Aucune catégorie trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
