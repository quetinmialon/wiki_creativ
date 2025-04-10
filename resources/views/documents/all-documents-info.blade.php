@extends('layouts.app')

@section('content')
<x-search-bar.document-search-bar/>

<div class="max-w-full mx-auto p-6 bg-white rounded-lg shadow-md m-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Liste des documents accessibles</h1>
        <a href="{{ route('documents.index') }}"
           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Catégories accessibles
        </a>
    </div>
    @if($categories->isEmpty())
        <p class="text-gray-500">Aucune catégorie trouvée.</p>
    @else
        @foreach($categories as $category)
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
                                <!-- Lien pour afficher le document ou pour demander à y acceder si l'utilisateur n'a pas le role adéquat -->
                                @if (
                                    //check if document and user share a role name to access it
                                    in_array($category->role->name, Auth::user()->roles->pluck('name')->toArray())
                                    // or if a temporary permission exists on this document for the current user
                                    || Gate::allows('access-document', $document)
                                )
                                    <a href="{{ route('documents.show', $document->id) }}" class="inline-block mt-2 px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                        👀 Voir le document
                                    </a>
                                @else
                                    <a href="{{ route('permissions.requestForm', $document->id) }}" class="inline-block mt-2 px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                        🔐 Demander l'accès
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
