@extends('layouts.app')

@section('content')

<h1 class="text-xl text-[#126C83] text-center">Toutes les catégories</h1>

<x-search-bar.document-search-bar/>

<div class="max-w-full mx-auto p-6 bg-white rounded-lg shadow-md m-4 flex flex-col">

    @if($categories->isEmpty())
        <p class="text-gray-500">Aucune catégorie trouvée.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
            @if($category->documents->isEmpty())
                @continue
            @else
            <div class="mb-8">
                <div class="rounded-md shadow-lg h-96 max-h-96 flex flex-col overflow-scroll">
                    <!-- Titre de la catégorie -->
                    <h2 class="text-lg text-white bg-[#126C83] mb-4 p-2 rounded-t-md text-center">
                        {{ $category->role->name}} : {{$category->name}}
                    </h2>
                        <ul class="flex flex-col gap-4 mb-4 px-4">
                            @foreach($category->documents as $document)
                                <li class="flex flex-col gap-1 border-b-2 pb-2">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-md text-gray-900">{{ $document->name }}</h4>
                                        @if (
                                            in_array($category->role->name, Auth::user()->roles->pluck('name')->toArray())
                                            || Gate::allows('access-document', $document)
                                        )
                                            <a href="{{ route('documents.show', $document->id) }}">
                                                <img src="{{ asset('images/see.png') }}" alt="voir le document {{ $document->name }}" arya-label="voir le document {{ $document->name }}"/>
                                            </a>
                                        @else
                                            <a href="{{ route('permissions.requestForm', $document->id) }}">
                                                <img src="{{ asset('images/lock.png') }}" alt="demander l'accès au document {{ $document->name }}" arya-label="demander l'accès au document {{ $document->name }}"/>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-700">
                                        {{ $document->excerpt }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <button class="mt-auto flex justify-end">
                        <a href="{{ route('documents.byCategory', $category->id) }}"
                            class="text-[#126C83] hover:text-[#35A5A7] underline p-4">
                            Tous les documents de la catégorie ->
                        </a>
                    </button>
                </div>

            </div>
        @endforeach
        </div>
    @endif
    <div class="flex justify-end mb-4">
        <a href="{{ route('documents.index') }}"
           class="text-[#126C83] hover:text-[#35A5A7] underline">
            Afficher uniquement les catégories avec des documents accessibles
        </a>
    </div>
</div>
@endsection
