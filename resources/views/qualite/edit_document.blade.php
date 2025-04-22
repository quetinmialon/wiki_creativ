@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mettre à jour un document et y ajouter une nomenclature</h1>

    <form action="{{ route('qualite.update',$document->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $document->id }}">

        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nom du Document</label>
            <input type="text" name="name" id="name" value="{{ old('name', $document->name) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            @error('name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <!-- Nomenclature -->
        <div>
            <label for="formated_name" class="block text-sm font-medium text-gray-700">Nomenclature</label>
            <input type="text" name="formated_name" id="formated_name" value="{{ old('formated_name', $document->formated_name) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            @error('formated_name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Extrait -->
        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700">Résumé</label>
            <textarea name="excerpt" id="excerpt" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('excerpt', $document->excerpt) }}</textarea>
            @error('excerpt')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Editeur WYSIWYG -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
            <div id="toolbar" class="mb-2">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-header" value="1"></button>
                <button class="ql-header" value="2"></button>
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
            </div>
            <div id="editor" class="border rounded p-2 h-64 overflow-y-auto">{!! old('content', $document->content) !!}</div>
            <input type="hidden" name="content" id="content" value="{{ old('content', $document->content) }}">
            @error('content')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catégories -->
        <div>
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
        </div>

        <!-- Boutons -->
        <div class="flex justify-end gap-4">
            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                Mettre à jour
            </button>
            <a href="{{ route('qualite.index') }}"
               class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-600">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection


