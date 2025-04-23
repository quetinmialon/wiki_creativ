@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10 px-6">
    <h1 class="text-3xl font-bold mb-6">Vos identifiants </h1>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Vos identifiants personnels</h2>
        @if($personnal_credentials->isEmpty())
            <p class="text-gray-600">Aucun identifiant personnel disponible.</p>
        @else
            <ul class="space-y-4">
                @foreach($personnal_credentials as $credential)
                    <li class="p-4 bg-gray-100 rounded-md shadow-md flex justify-between items-center">
                        <div>
                            <span class="font-bold text-lg">{{ $credential->destination }}</span>
                            <span class="text-gray-600">- {{ $credential->username }}</span>
                            <div class="mt-2">
                                <button onclick="copyPassword({{ $credential->id }})" class="text-blue-500 hover:underline">
                                    Copier le mot de passe
                                </button>
                                <span id="password-{{ $credential->id }}" class="hidden">{{ $credential->password }}</span>
                            </div>

                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('credentials.edit', $credential->id) }}" class="text-blue-500 hover:underline">Modifier</a>
                            <form action="{{ route('credentials.destroy', $credential->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce log ?')"
                                        class="text-red-500 hover:underline">Supprimer</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div>
        <h2 class="text-2xl font-semibold mb-4">identifiants partagés avec vos rôles</h2>
        @if(empty($shared_credentials))
            <p class="text-gray-600">Aucun identifiant partagé disponible.</p>
        @else
            @foreach($shared_credentials as $roleName => $credentials)
                <h3 class="text-xl font-semibold mt-6">{{ $roleName }}</h3>
                <ul class="space-y-4">
                    @foreach($credentials as $credential)
                        <li class="p-4 bg-gray-100 rounded-md shadow-md flex justify-between items-center">
                            <div>
                                <span class="font-bold text-lg">{{ $credential->destination }}</span>
                                <span class="text-gray-600">- {{ $credential->username }}</span>
                                <div class="mt-2">
                                    <button onclick="copySharedPassword({{ $credential->id }})" class="text-blue-500 hover:underline">
                                        Voir mot de passe
                                    </button>
                                    <span id="shared-password-{{ $credential->id }}" class="hidden bg-gray-200 px-2 py-1 rounded-md">{{ $credential->password }}</span>
                                </div>
                            </div>
                            @if(Gate::allows('manage-shared-credential',$credential))
                            <div class="flex space-x-4">
                                <a href="{{ route('credentials.edit', $credential->id) }}" class="text-blue-500 hover:underline">Modifier</a>
                                <form action="{{ route('credentials.destroy', $credential->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce log ?')"
                                            class="text-red-500 hover:underline">Supprimer</button>
                                </form>
                            </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        @endif
    </div>

    <a href="{{ route('credentials.create') }}" class="mt-8 inline-block px-6 py-2 bg-blue-500 text-white font-semibold rounded-md shadow hover:bg-blue-600">
        Ajouter un log
    </a>
</div>
@endsection
