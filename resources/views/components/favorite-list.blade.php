@props(['favorites'])

<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-6">Mes Favoris</h1>

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
                            <a href="{{ route('documents.show', $favorite->document->id) }}"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Voir le document
                            </a>
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
