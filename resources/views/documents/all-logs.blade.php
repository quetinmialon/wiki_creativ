@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-semibold text-center text-[#126C83] mb-4">Liste des ouvertures des documents</h1>
    <div class="bg-white shadow rounded-lg p-6">
        @if ($logs->isEmpty())
            <p class="text-gray-500">Aucun log enregistré.</p>
        @else
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-200 px-4 py-2 text-left">Utilisateur</th>
                        <th class="border border-gray-200 px-4 py-2 text-left">Document</th>
                        <th class="border border-gray-200 px-4 py-2 text-left">Date d'ouverture</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="border border-gray-200 px-4 py-2">{{ $log->user->name ?? 'anonyme' }}</td>
                            <td class="border border-gray-200 px-4 py-2">{{ $log->document->name ?? 'document supprimé'}}</td>
                            <td class="border border-gray-200 px-4 py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
<div class="mt-6">
    {{ $logs->links() }}
</div>
@endsection
