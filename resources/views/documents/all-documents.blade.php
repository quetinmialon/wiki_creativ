@extends('layouts.admin')

@section('content')

<x-search-bar.document-search-bar/>

<div class="max-w-full mx-auto p-6 bg-white rounded-lg shadow-md m-4">
    <h1 class="text-2xl font-bold mb-6">Liste des Documents par Catégorie</h1>
    @if($categories->isEmpty())
        <p class="text-gray-500">Aucune catégorie trouvée.</p>
    @else
        @foreach($categories as $category)
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Catégorie : {{ $category->name }}</h2>
            @foreach($category->documents as $document)
                <div class="mb-8">
                    <!-- Titre de la catégorie -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="p-4 border rounded-md bg-gray-50 shadow-sm">
                                <h4 class="text-md font-semibold text-gray-900">{{ $document->name }}</h4>
                                <p class="text-sm text-gray-600 mb-2">Auteur : {{ $document->author?->name ?? 'Inconnu' }}</p>
                                <p class="text-sm text-gray-700">{{ $document->excerpt }}</p>
                                <a href="{{ route('documents.show', $document->id) }}" class="inline-block mt-2 px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                    Voir le document
                                </a>
                                <!-- Actions sur le document -->
                                <div class="flex gap-2 mt-2">
                                    <a href="{{ route('documents.edit', $document->id) }}"
                                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                        Modifier le document
                                    </a>
                                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">
                                            Supprimer le document
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                </div>
            @endforeach
        @endforeach
    @endif
</div>

@endsection
