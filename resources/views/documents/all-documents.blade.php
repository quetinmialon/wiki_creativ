@extends('layouts.admin')

@section('content')

<div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-md">
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
                            </div>
                        </div>
                </div>
            @endforeach
        @endforeach
    @endif
</div>

@endsection
