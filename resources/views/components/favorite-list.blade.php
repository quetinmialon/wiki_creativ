@props(['favorites'])

<div class="container mx-auto">
    <h1 class="text-2xl  mt-4 mb-6 text-[#126C83] text-center">Mes Favoris</h1>

    @if($favorites->isEmpty())
        <p class="text-gray-600">Vous n'avez encore aucun document en favoris.</p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($favorites as $favorite)
                @if($favorite->document)
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $favorite->document->name }}</h2>
                            <p class="text-sm text-gray-600">Auteur : {{ $favorite->document->author->name ?? 'Inconnu' }}</p>
                            <p class="text-sm text-gray-600">{{ $favorite->document->excerpt }}</p>
                        </div>
                        <div class="flex gap-2">
                            @php
                                $isFavorited = app(App\Services\FavoriteService::class)->isFavorited($favorite->document->id, Auth::id());
                            @endphp
                            <a
                                class="hover:cursor-pointer"
                                id="favorite-btn-{{ $favorite->document->id }}"
                                onclick="toggleFavorite({{ $favorite->document->id }})">
                                {!! $isFavorited
                                    ? "<img src='" . asset('images/favorite.png') . "' alt='retirer des favoris'/>"
                                    : "<img src='" . asset('images/notfavorite.png') . "' alt='ajouter aux favoris'/>"
                                !!}
                            </a>
                            @if(Gate::allows('view-document',$favorite->document)|| Gate::allows('access-document',$favorite->document))
                                <a href="{{ route('documents.show', $favorite->document->id) }}">
                                    <img src="{{  asset('images/see.png') }}" alt="voir le document {{ $favorite->document->name }}" arya-label="voir le document {{ $favorite->document->name }}"/>
                                </a>
                            @else
                                <a href="{{ route('permissions.requestForm', $favorite->document->id) }}">
                                    <img src="{{  asset('images/lock.png') }}" alt="demander l'accès au document {{ $favorite->document->name }}" arya-label="demander l'accès au document {{ $favorite->document->name }}"/>
                                </a>
                            @endif
                        </div>
                    </li>
                @else
                    <li class="py-4">
                        <p class="text-gray-500">Document introuvable (peut avoir été supprimé).</p>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
