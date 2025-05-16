@extends('layouts.admin')

@section('content')
 <h1 class="text-xl font-semibold text-[#126C83] text-center">Liste des Catégories</h1>
<div class="container mx-auto mt-6">
    <table class="w-full rounded-t-md">
        <thead>
            <tr class="bg-[#126C83] text-white">
                <th class="px-4 py-2 rounded-tl-md">Nom</th>
                <th class="px-4 py-2"> Roles associé </th>
                <th class="px-4 py-2 rounded-tr-md">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td class="pl-8 pr-4 py-2">{{ $category->name }}</td>
                    <td class="px4 py-2 text-center">{{$category->role->name == 'default' ? 'public' : $category->role->name }}</td>
                    <td class=" px-4 py-2 flex flex-row justify-around">
                        <a href="{{ route('documents.byCategory', ['id' => $category->id]) }}" class="text-[#126C83] underline hover:text-[#35A5A7]">accéder aux documents de la catégorie</a>
                        <div class="flex flex-row space-x-2">
                            <a href="{{ route('categories.edit', $category->id) }}">
                                <img src="{{ asset('images/edit.png') }}" alt="modifier la catégorie"/>
                            </a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                    <img src="{{ asset('images/delete.png') }}" alt="supprimer la catégorie"/>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class=" px-4 py-2 text-center">Aucune catégorie trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-8 flex justify-center">
        <a href="{{ route('categories.create') }}" class="bg-[#126C83] text-white px-4 py-2 rounded hover:bg-[#126C83]">Ajouter une catégorie</a>
    </div>
</div>
@endsection
