@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-semibold text-center text-[#126C83] mb-6">Documents à nomenclaturer</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded shadow">
            <thead>
                <tr class="bg-[#126C83] text-white">
                    <th class="px-4 py-2 text-left">Titre</th>
                    <th class="px-4 py-2 text-left">Résumé</th>
                    <th class="px-4 py-2 text-left">Nomenclature</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($document as $doc)
                    <tr class="border-t border-gray-200">
                        <td class="px-4 py-2 font-semibold">{{ $doc->name ?? 'Document sans titre' }}</td>
                        <td class="px-4 py-2">{{ Str::limit($doc->excerpt, 150) }}</td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('qualite.addNormedName', $doc->id) }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $doc->id }}">
                                <input type="text" name="formated_name" placeholder="Nomenclature..." required
                                       class="border border-gray-300 rounded px-2 py-1 w-full sm:w-40">
                                <button type="submit" class="bg-[#35A5A7] text-white px-3 py-1 rounded hover:bg-[#126C83] text-sm">
                                    Enregistrer
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('qualite.edit', ['id' => $doc->id]) }}"
                               class="text-sm text-[#126C83] underline hover:text-[#35A5A7]">
                                Consulter / Modifier
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Aucun document en attente de nomenclature.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex justify-end mt-4">
        <a href="{{ route('qualite.documents') }}"
            class="px-4 py-2 bg-[#35A5A7] text-white rounded hover:bg-[#126C83]">
            Documents nomenclaturés
        </a>
    </div>
</div>

@endsection
