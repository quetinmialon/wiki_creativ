@extends('layouts.app')

@section('content')
@if(!$document->formated_name)
    <div class="bg-red-500 text-white text-center py-4 mb-4 rounded">
        <p class="text-lg font-bold">⚠️ Attention : Le service qualité n'a pas encore validé ce document et il ne respecte peut-être pas les normes de qualité de l'entreprise.</p>
    </div>
@endif

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $document->name }}</h1>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700">Auteur :</h2>
        <p class="text-gray-600">{{ $document->author?->name ?? 'Inconnu' }}</p>
    </div>
    @if($document->formated_name)
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Nomenclature :</h2>
            <p class="text-gray-600">{{ $document->formated_name }}</p>
        </div>
    @endif
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700">Résumé :</h2>
        <p class="text-gray-600">{{ $document->excerpt }}</p>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700">Contenu :</h2>
        <p class="text-gray-600 whitespace-pre-line">{!! $document->content !!}</p>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700">Catégories :</h2>
        @if($document->categories->isEmpty())
            <p class="text-gray-600">Aucune catégorie associée.</p>
        @else
            <ul class="list-disc pl-6 text-gray-600">
                @foreach($document->categories as $category)
                    <li>{{ $category->name }}</li>
                @endforeach
            </ul>
        @endif
    </div>


    <a href="{{ route('documents.index') }}" class="inline-block ml-2 px-4 py-2 text-sm text-white bg-gray-500 rounded hover:bg-gray-600">
        Retour à la liste
    </a>
    @php
        $isFavorited = app(App\Services\FavoriteService::class)->isFavorited($document->id, Auth::id());
    @endphp

    <button
        id="favorite-btn-{{ $document->id }}"
        class="inline-block ml-2 px-4 py-2 text-sm text-white rounded {{ $isFavorited ? 'bg-red-500 hover:bg-red-600'  : 'bg-blue-500 hover:bg-blue-600' }}"
        onclick="toggleFavorite({{ $document->id }})">
        {{ $isFavorited ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
    </button>

    @if(Gate::allows('manage-document', $document))
        <a href="{{ route('documents.edit', $document->id) }}" class="inline-block px-4 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
            Modifier le document
        </a>
        <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="inline-block ml-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                Supprimer le document
            </button>
        </form>
    @endif
</div>

@endsection
