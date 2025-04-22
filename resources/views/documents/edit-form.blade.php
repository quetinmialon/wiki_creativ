@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Modifier le Document</h1>

    <form action="{{ route('documents.update', $document->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
            <input type="text" name="name" id="name" value="{{ old('name', $document->name) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            @error('name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Extrait -->
        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700">Résumé</label>
            <textarea name="excerpt" id="excerpt" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('excerpt', $document->excerpt) }}</textarea>
            @error('excerpt')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contenu (WYSIWYG) -->
        <div id="toolbar">
            <button class="ql-bold"></button>
            <button class="ql-italic"></button>
            <button class="ql-underline"></button>
            <button class="ql-header" value="1"></button>
            <button class="ql-header" value="2"></button>
            <button class="ql-list" value="ordered"></button>
            <button class="ql-list" value="bullet"></button>
        </div>
        <!-- Éditeur de texte -->
        <div id="editor" class="border rounded p-2">{!! old('content', $document->content) !!}</div>
        <!-- Champ caché qui stocke le HTML -->
        <input type="hidden" name="content" id="content" value="{{ old('content', $document->content) }}">

        <!-- Catégories -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-2">Catégories</h3>
            @foreach($roles as $role)
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-600">{{ $role->name }}</h4>
                    @if($role->categories->isEmpty())
                        <p class="text-sm text-gray-500">Aucune catégorie disponible pour ce rôle.</p>
                    @else
                        <div class="space-y-2 mt-2">
                            @foreach($role->categories as $category)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="categories_id[]" value="{{ $category->id }}"
                                           {{ in_array($category->id, $document->categories->pluck('id')->toArray()) ? 'checked' : '' }}
                                           class="form-checkbox text-blue-500">
                                    <span class="ml-2 text-gray-700">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            @error('categories_id')
                <p class="text-sm text-red-600">{!! $message !!}</p>
            @enderror
        </div>

        <!-- Boutons -->
        <div class="flex justify-end space-x-3">
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                Enregistrer les modifications
            </button>
            <a href="{{ route('documents.show', $document->id) }}" class="px-4 py-2 text-sm text-white bg-gray-500 rounded hover:bg-gray-600">
                Annuler
            </a>
        </div>
    </form>
</div>

@endsection
