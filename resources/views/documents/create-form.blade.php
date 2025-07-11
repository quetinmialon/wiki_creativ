@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-xl font-semibold text-[#126C83] mb-6 text-center">Créer un Document</h1>

    <!-- display session messages, including success and errors -->
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

    <!-- Document creation form -->
    <form action="{{ route('documents.store') }}" method="POST">
        @csrf
        <!-- name field -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nom du Document</label>
            <input type="text" id="name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- excerpt field -->
        <div class="mb-4">
            <label for="excerpt" class="block text-sm font-medium text-gray-700">Résumé</label>
            <textarea id="excerpt" name="excerpt" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
            @error('excerpt')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- WYSIWYG field -->

        <p>Document </p>
        <div id="toolbar">
            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-script" value="sub"></button>
                <button class="ql-script" value="super"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-header" value="1"></button>
                <button class="ql-header" value="2"></button>
                <button class="ql-header" value="3"></button>
                <button class="ql-header" value="4"></button>
                <button class="ql-blockquote"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-link"></button>
            </span>
        </div>
        <!-- html field that display the preview -->
        <div id="editor" class="border rounded p-2 min-h-64"></div>
        <!-- hidden field that stores the HTML -->
        <input type="hidden" name="content" id="content">
        <div class="flex items-center my-4">
            <label for="public" class="mr-2 text-sm font-medium text-gray-700">Document publique</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="public" name="categories_id[]" value="1" class="sr-only peer" >
                <div class="w-11 h-6 bg-gray-600 rounded-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#35A5A7] peer peer-checked:bg-[#35A5A7] after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
            </label>
        </div>
        <!-- roles and categories -->
        <div class="mb-6" id="categoriesDiv">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rôles et Catégories</label>
            @foreach($roles as $role)
                @if(!str_contains($role->name, 'Admin '))
                @if(!str_contains($role->name, 'default'))
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
                @endif
                @endif
            @endforeach
            @error('categories_id')
                <p class="text-red-500 text-xs mt-1">{!! $message !!}</p>
            @enderror
        </div>

        <div class="flex justify-center">
            <button type="submit" class="p-4 bg-[#35A5A7] text-white px-4 py-2 rounded-md shadow-sm hover:bg-[#126C83]">
                Créer le Document
            </button>
        </div>
    </form>
</div>
@endsection
