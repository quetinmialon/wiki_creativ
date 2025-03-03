@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold mb-6">Créer un Document</h1>

    <!-- Affichage des messages de succès ou d'erreur -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulaire de création de document -->
    <form action="{{ route('documents.store') }}" method="POST">
        @csrf
        <!-- Champ 'name' -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nom du Document</label>
            <input type="text" id="name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Champ 'excerpt' -->
        <div class="mb-4">
            <label for="excerpt" class="block text-sm font-medium text-gray-700">Extrait</label>
            <textarea id="excerpt" name="excerpt" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
            @error('excerpt')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- WYSIWYG -->
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
        <div id="editor" class="border rounded p-2"></div>
        <!-- Champ caché qui stocke le HTML -->
        <input type="hidden" name="content" id="content">
        <!-- Rôles et catégories -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rôles et Catégories</label>
            @foreach($roles as $role)
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $role->name }}</h2>
                    <div class="mt-2 space-y-2">
                        @foreach($role->categories as $category)
                            <div class="flex items-center">
                                <input type="checkbox" id="category_{{ $category->id }}" name="categories_id[]" value="{{ $category->id }}" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-700">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            @error('categories_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Bouton de soumission -->
        <div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Créer le Document
            </button>
        </div>
    </form>
</div>
@endsection
