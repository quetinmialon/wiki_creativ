@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <div class="flex flex-row justify-between">
        <div>
        </div>
        <h1 class="text-xl font-bold text-[#126C83] mb-4 text-center">{{ $document->name }}</h1>
        <div class="flex flex-row gap-4">
            @php
                $isFavorited = app(App\Services\FavoriteService::class)->isFavorited($document->id, Auth::id());
            @endphp
            <a
                class="hover:cursor-pointer"
                id="favorite-btn-{{ $document->id }}"
                onclick="toggleFavorite({{ $document->id }})">
                {!! $isFavorited
                    ? "<img src='" . asset('images/favorite.png') . "' alt='retirer des favoris'/>"
                    : "<img src='" . asset('images/notfavorite.png') . "' alt='ajouter aux favoris'/>"
                !!}
            </a>
            @can('manage-document',$document)
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
            @endcan
        </div>
    </div>

    <div class="mb-2">
        <p class="text-gray-500 text-sm">
            Écrit par : {{ $document->author?->name ?? 'Inconnu' }}
            le {{ $document->created_at->format('d/m/Y à H:i') }}

            @if ($document->updated_at && $document->updated_at != $document->created_at && $document->updated_by)
                —
                modifié le {{ $document->updated_at->format('d/m/Y à H:i') }}
                par {{ $document->updator?->name ?? 'Inconnu' }}
            @endif
        </p>
    </div>


    <div class="mb-6">
        <h2 class="text-gray-700">Résumé :</h2>
        <p class="text-gray-600">{{ $document->excerpt }}</p>
    </div>

    <h2 class="text-gray-700"> Contenu du document</h2>
    <div class="border-2 border-grey-200 rounded-lg">
        <div class="mb-6 p-4">
            <p class="text-gray-600 whitespace-pre-line">{!! $document->content !!}</p>
        </div>

        @if($document->formated_name)
            <div class="flex justify-between items-center bg-[#126C83] text-white rounded-full px-6 py-4 text-sm w-4xl max-w-4xl m-4">
                <p class="leading-snug">
                    Duplication strictement interdite. Conception et réalisation par le Groupe Créative.
                    Tous droits de représentation, reproduction, d’adaptation, d’exploitation, même partielle, strictement interdits.
                </p>
                <div class="text-right ml-4">
                    <div class="bg-white text-[#126C83] font-bold rounded-full px-4 py-1 mb-1 inline-block text-sm">
                        {{ $document->formated_name }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="my-6">
        <h2 class="text-gray-700">Catégories :</h2>
        @if($document->categories->isEmpty())
            <p class="text-gray-600">Aucune catégorie associée.</p>
        @else
            <ul class="list-disc pl-6 text-gray-600">
                @foreach($document->categories as $category)
                    <a href="{{ route('documents.byCategory', $category->id) }}">
                        <li class="text-[#126C83] underline hover:text-[#35A5A7]">{{ $category->name }}</li>
                    </a>
                @endforeach
            </ul>
        @endif
    </div>
    <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm text-white bg-[#35A5A7] rounded hover:bg-[#126C83]">
        Retour
    </a>
</div>

@endsection
