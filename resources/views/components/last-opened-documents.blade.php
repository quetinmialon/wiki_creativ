@props(['logs'])

<div class="container mx-auto h-96">
    <h1 class="text-2xl text-[#126C83] m-4 text-center">Derniers documents ouverts</h1>

    @if($logs->isEmpty())
        <p class="p-2">Aucun document récemment ouvert.</p>
    @else
        <ul class="divide-y divide-gray-200 m-4">
            @foreach($logs as $log)
                @if($log->document)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $log->document->name }}</h2>
                            <p class="text-sm text-gray-600">Ouvert le : {{ $log->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="flex flex-row px-2 gap-4">
                            @if(Gate::allows('view-document',$log->document)|| Gate::allows('access-document',$log->document))
                                <a href="{{ route('documents.show', $log->document->id) }}">
                                    <img src="{{  asset('images/see.png') }}" alt="voir le document {{ $log->document->name }}" arya-label="voir le document {{ $log->document->name }}"/>
                                </a>
                            @else
                                <a href="{{ route('permissions.requestForm', $log->document->id) }}">
                                    <img src="{{  asset('images/lock.png') }}" alt="demander l'accès au document {{ $log->document->name }}" arya-label="demander l'accès au document {{ $log->document->name }}"/>
                                </a>
                            @endif
                        </div>
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
