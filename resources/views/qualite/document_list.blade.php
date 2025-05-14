@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-semibold text-center text-[#126C83] mb-6">Documents nomenclaturés</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded shadow">
            <thead>
                <tr class="bg-[#126C83] text-white">
                    <th class="py-2 px-4 border-b">Nom</th>
                    <th class="py-2 px-4 border-b">Extrait</th>
                    <th class="py-2 px-4 border-b">Nomenclature</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($document as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b text-center font-medium">{{ $doc->name }}</td>
                        <td class="py-2 px-4 border-b">{{ Str::limit($doc->excerpt, 150) }}</td>
                        <td class="py-2 px-4 border-b">
                            <form method="POST" action="{{ route('qualite.addNormedName', $doc->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $doc->id }}">
                                <input type="text" name="formated_name" placeholder="Nomenclature..."
                                    class="border border-gray-300 rounded px-2 py-1 w-40"
                                    value="{{ old('formated_name', $doc->formated_name) }}">
                                <button type="submit" class="bg-[#35A5A7] text-white px-3 py-1 rounded hover:bg-[#126C83] text-sm">
                                    Changer
                                </button>
                            </form>
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            <a href="{{ route('qualite.edit', ['id' => $doc->id]) }}"
                               class="text-[#126C83] underline hover:text-[#35A5A7]">
                                Consulter / Modifier
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">Aucun document en attente de nomenclature.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex justify-end mt-4">
        <a href="{{ route('qualite.index') }}" class="px-4 py-2 bg-[#35A5A7] text-white rounded hover:bg-[#126C83]">
            Documents à nomenclaturer
        </a>
    </div>
    <div class="mt-4">
        {{ $document->links() }}
    </div>
</div>
@endsection


