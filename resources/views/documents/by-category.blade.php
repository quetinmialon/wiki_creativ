@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Documents pour la catégorie : {{ $category->name }}</h1>

    @if ($documents->isEmpty())
        <p class="text-gray-600">Aucun document trouvé pour cette catégorie.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($documents as $document)
                <div class="bg-white shadow-md rounded-md p-4 border border-gray-200">
                    <h2 class="text-lg font-semibold text-blue-600 mb-2">{{ $document->name }}</h2>
                    <p class="text-gray-700 mb-4">{{ $document->excerpt }}</p>
                    <p class="text-sm text-gray-500 mb-4">Auteur : {{ $document->author->name }}</p>
                    <a href="{{ route('documents.show', $document->id) }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                       👀 Voir le document
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('documents.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour aux documents
        </a>
    </div>
</div>
@endsection
