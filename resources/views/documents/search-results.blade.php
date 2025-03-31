@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">Résultat de la recherche {{$query}}</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($documents as $document)
        <div class="p-4 border rounded-md bg-gray-50 shadow-sm">
            <h4 class="text-md font-semibold text-gray-900">{{ $document->name }}</h4>
            <p class="text-sm text-gray-600 mb-2">Auteur : {{ $document->author?->name ?? 'Inconnu' }}</p>
            <p class="text-sm text-gray-700">{{ $document->excerpt }}</p>
            <!-- Lien pour afficher le document ou pour demander à y acceder si l'utilisateur n'a pas le role adéquat -->
                <a href="{{ route('documents.show', $document->id) }}" class="inline-block mt-2 px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                    Voir le document
                </a>
        </div>
    @endforeach
</div>
@endsection
