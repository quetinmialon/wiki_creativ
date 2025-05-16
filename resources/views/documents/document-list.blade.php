@extends('layouts.app')

@section('content')

<h1 class="text-xl text-[#126C83] text-center">Documents et catégories</h1>

<x-search-bar.document-search-bar/>

<div class="max-w-full mx-auto p-6 bg-white rounded-lg shadow-md m-4 flex flex-col">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @if($categories->isEmpty())
            <p class="text-gray-500">Aucune catégorie trouvée.</p>
        @else
            @foreach($categories as $category)
                    @if($category->documents->isEmpty())
                        @continue
                    @else
                    <div class=" rounded-md shadow-lg h-96 max-h-96 flex flex-col overflow-scroll">
                        <!-- Titre de la catégorie -->
                        <h2 class="text-lg text-white bg-[#126C83] mb-4 p-2 rounded-t-md text-center">{{ $category->name }}</h2>

                        <ul class="flex flex-col gap-4 mb-4 px-4">
                            @foreach($category->documents as $document)

                                <li class="flex flex-row justify-between pl-4">
                                    <h4 class="text-md text-gray-900">{{ $document->name }}</h4>
                                    <a href="{{ route('documents.show', $document->id) }}">
                                        <img src="{{  asset('images/see.png') }}" alt="voir le document {{ $document->name }}" arya-label="voir le document {{ $document->name }}"/>
                                    </a>
                                </li>
                                <div class="border-b-2 text-xs">
                                    {{ $document->excerpt }}
                                </div>
                            @endforeach
                        </ul>
                        <div class="mt-auto flex justify-end">
                            <a href="{{ route('documents.byCategory', $category->id) }}"
                                class="text-[#126C83] hover:text-[#35A5A7] underline p-4 ">
                                Tous les documents de la catégorie ->
                            </a>
                        </div>
                    </div>
                    @endif
            @endforeach
        @endif
    </div>
    <div class="flex justify-end mb-4">
        <a href="{{ route('documents.allDocumentsInfo') }}"
            class="text-[#126C83] hover:text-[#35A5A7] underline">
            Voir aussi les documents et les catégories non accessibles
        </a>
    </div>
</div>
@endsection
