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
        <div class="flex items-center mb-4">
            <label for="public" class="mr-2 text-sm font-medium text-gray-700">Document publique</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="public" name="categories_id[]" value="1" class="sr-only peer" {{ in_array(1, $document->categories->pluck('id')->toArray()) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 peer dark:bg-gray-700 peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
            </label>
        </div>
        <!-- Catégories -->
        <div id="categoriesDiv">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Catégories</h3>
            @foreach($roles as $role)
            @if(!str_contains($role->name, 'Admin '))
            @if(!str_contains($role->name, 'default'))
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
            @endif
            @endif
            @endforeach
            @error('categories_id')
                <p class="text-sm text-red-600">{!! $message !!}</p>
            @enderror
        </div>

        <!-- Boutons -->
        <div class="flex justify-end space-x-3">
            <button type="submit" class="px-4 py-2 text-sm text-white bg-[#35A5A7] rounded hover:bg-[#126C83]">
                Enregistrer les modifications
            </button>
            <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm text-white bg-[#35A5A7] rounded hover:bg-[#126C83]">
                Retour
            </a>
        </div>
    </form>
</div>
@endsection
