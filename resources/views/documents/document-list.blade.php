@extends('layouts.app')

@section('content')

<x-search-bar.document-search-bar/>

<div class="max-w-full mx-auto p-6 bg-white rounded-lg shadow-md m-4">
    <h1 class="text-2xl font-bold mb-6">Liste des documents accessibles</h1>

    <a href="{{ route('documents.allDocumentsInfo') }}"> Voir tous les documents</a>

    @if($categories->isEmpty())
        <p class="text-gray-500">Aucune catégorie trouvée.</p>
    @else
        @foreach($categories as $category)
        <button>
            <a href="{{ route('documents.byCategory', $category->id) }}"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Voir tous les documents de cette catégorie
            </a>
        </button>
            <div class="mb-8">
                <!-- Titre de la catégorie -->
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Catégorie : {{ $category->name }}</h2>

                @if($category->documents->isEmpty())
                    <p class="text-gray-500">Aucun document pour cette catégorie.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($category->documents as $document)
                            <div class="p-4 border rounded-md bg-gray-50 shadow-sm">
                                <h4 class="text-md font-semibold text-gray-900">{{ $document->name }}</h4>
                                <p class="text-sm text-gray-600 mb-2">Auteur : {{ $document->author?->name ?? 'Inconnu' }}</p>
                                <p class="text-sm text-gray-700">{{ $document->excerpt }}</p>
                                <a href="{{ route('documents.show', $document->id) }}" class="inline-block mt-2 px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                    Voir le document
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
