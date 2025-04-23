@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Documents nomenclaturés</h1>
    <button class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        <a href="{{ route('qualite.index') }}">Documents à nomenclaturer</a>
    </button>

    @foreach($document as $doc)
        <div class="bg-white shadow rounded p-4 mb-4">
            <h2 class="text-lg font-semibold">{{ $doc->name ?? 'Document sans titre' }}</h2>
            <p class="text-sm text-gray-600 mb-2">ID : {{ $doc->id }}</p>
            <p class="mb-2">{{ Str::limit($doc->excerpt, 150) }}</p>

            <form method="POST" action="{{ route('qualite.addNormedName', $doc->id) }}" class="flex items-center gap-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $doc->id }}">
                <input type="text" name="formated_name" placeholder="Nomenclature..."
                       class="border border-gray-300 rounded px-2 py-1 w-1/2" value="{{ old('formated_name', $doc->formated_name) }}">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                    Changer la nomenclature
                </button>
                <a href="{{ route('qualite.edit', ['id' => $doc->id]) }}"
                   class="text-sm text-blue-600 hover:underline">Consulter et modifier le document</a>
            </form>
        </div>
    @endforeach

    @if($document->isEmpty())
        <p>Aucun document en attente de nomenclature.</p>
    @endif
</div>
@endsection


