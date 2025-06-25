@extends('layouts.admin')

@section('content')

<x-search-bar.document-search-bar/>

<h1 class="text-2xl font-bold mb-6">Résultat de la recherche {{$query}}</h1>

<div class="flex flex-col">
    @foreach ($documents as $document)
        <div class="bg-white p-4 border-b border-gray-200">
            <div class="flex flex-row justify-between">
                <h2 class="text-lg font-semibold text-[#126C83] mb-2">{{ $document->name }}</h2>
                <p class="text-sm text-gray-500 mb-4">Rédigé le {{ $document->created_at->format('d/m/Y') }}</p>
            </div>
            <p class="text-gray-700 mb-4">Résumé : {{ $document->excerpt }}</p>

            <div class="flex flex-row justify-between">
                <p class="text-sm text-gray-500 mb-4">Écrit par {{ $document->author->name }}</p>

                <div class="flex flex-row px-2 gap-4">
                    <a href="{{ route('documents.show', $document->id) }}">
                        <img src="{{  asset('images/see.png') }}" alt="voir le document {{ $document->name }}"
                        aria-label="voir le document {{ $document->name }}"/>
                    </a>
                    <a href ="{{ route('documents.edit', $document->id) }}" alt="modifier le document {{ $document->name }}">
                        <img src="{{ asset('images/edit.png') }}"/>
                    </a>

                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le document {{$document->name}} ?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; padding: 0;">
                            <img src="{{ asset('images/delete.png') }}" alt="supprimer le document {{$document->name}}" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
<a href="{{ url()->previous() }}" class="px-4 py-2 hover:text-white bg-white shadow-md rounded hover:bg-[#126C83] text-[#126C83]">
    Retour
</a>
@endsection
