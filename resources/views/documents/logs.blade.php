@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Logs pour le document : {{ $document->name }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        @if ($logs->isEmpty())
            <p class="text-gray-500">Aucun log trouvé pour ce document.</p>
        @else
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-200 px-4 py-2 text-left">Utilisateur</th>
                        <th class="border border-gray-200 px-4 py-2 text-left">Date d'ouverture</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="border border-gray-200 px-4 py-2">{{ $log->user->name }}</td>
                            <td class="border border-gray-200 px-4 py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <a href="{{ route('documents.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Retour</a>
</div>
@endsection
