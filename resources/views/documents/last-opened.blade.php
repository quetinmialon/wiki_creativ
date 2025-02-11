@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-xl font-bold mb-4">Derniers documents ouverts</h1>

    @if($logs->isEmpty())
        <p>Aucun document récemment ouvert.</p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($logs as $log)
                @if($log->document)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $log->document->name }}</h2>
                            <p class="text-sm text-gray-600">Ouvert le : {{ $log->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <a href="{{ route('documents.show', $log->document->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Voir le document
                        </a>
                    </li>
                @else
                    <li class="py-2">
                        <p class="text-gray-500">Document introuvable (peut avoir été supprimé).</p>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
@endsection
