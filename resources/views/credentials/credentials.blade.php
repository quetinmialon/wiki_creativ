@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold text-center text-[#126C83] mb-6">Gestionnaire de mot de passe</h1>


<div class="flex w-full divide-x divide-gray-200">
    <div class="container mb-8 overflow-y-scroll pr-4">
        <h2 class="text-[#126C83] font-semibold mb-6 text-center">Vos identifiants</h2>
    @if($personnal_credentials->isEmpty())
        <p class="text-gray-600">Aucun identifiant personnel disponible.</p>
    @else
        <table class="min-w-full bg-white rounded-md">
            <thead>
                <tr>
                    <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Destination</th>
                    <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Nom d'utilisateur</th>
                    <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personnal_credentials as $credential)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ $credential->destination }}</td>
                        <td class="py-2 px-4">{{ $credential->username }}</td>
                        <td class="py-2 px-4">
                            <span id="password-{{ $credential->id }}" class="hidden">{{ $credential->password }}</span>
                            <div class="flex space-x-4">
                                <button onclick="copyPassword({{ $credential->id }})" class="text-blue-500 hover:underline">
                                    <img src="{{ asset('images/copy.png') }}" alt="copier le mot de passe"/>
                                </button>
                                <a href="{{ route('credentials.edit', $credential->id) }}" class="text-blue-500 hover:underline">
                                    <img src="{{ asset('images/edit.png') }}" alt="modifier l'identifiant"/>
                                </a>
                                <form action="{{ route('credentials.destroy', $credential->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet identifiant ?')"
                                            class="text-red-500 hover:underline">
                                            <img src="{{ asset('images/delete.png') }}" alt="supprimer l'identifiant"/>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    </div>

    <div class="container mb-8 overflow-y-scroll pl-4">
        <h2 class="text-[#126C83] font-semibold mb-4 text-center">Identifiants partagés</h2>
        @if(empty($shared_credentials))
            <p class="text-gray-600 align-self-center">Aucun identifiant partagé disponible.</p>
        @else
            @foreach($shared_credentials as $roleName => $credentials)
                <table class="min-w-full bg-white rounded-md mb-6">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Destination</th>
                            <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Nom d'utilisateur</th>
                            <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Partagé avec le rôle</th>
                            <th class="py-2 px-4 text-left text-[#126C83] font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($credentials as $credential)
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $credential->destination }}</td>
                                <td class="py-2 px-4">{{ $credential->username }}</td>
                                <td class="py-2 px-4">{{ $credential->role->name }}</td>
                                <td class="py-2 px-4">

                                    <div class="flex space-x-4 mt-2">
                                        <span id="shared-password-{{ $credential->id }}" class="hidden bg-gray-200 px-2 py-1 rounded-md">{{ $credential->password }}</span>
                                        <button onclick="copySharedPassword({{ $credential->id }})" class="text-blue-500 hover:underline">
                                            <img src="{{ asset('images/copy.png') }}" alt="copier le mot de passe"/>
                                        </button>
                                         @if(Gate::allows('manage-shared-credential', $credential))
                                        <a href="{{ route('credentials.edit', $credential->id) }}" class="text-blue-500 hover:underline">
                                            <img src="{{ asset('images/edit.png') }}" alt="modifier l'identifiant"/>
                                        </a>
                                        <form action="{{ route('credentials.destroy', $credential->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet identifiant ?')"
                                                    class="text-red-500 hover:underline">
                                                    <img src="{{ asset('images/delete.png') }}" alt="supprimer l'identifiant"/>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </ul>
            @endforeach
        @endif
    </div>
</div>
<div class="">
    <a href="{{ route('credentials.create') }}" class="inline-block px-4 py-1 bg-[#35A5A7] text-white rounded-md shadow hover:bg-[#126C83]">
        Ajouter un identifiant
    </a>
</div>
@endsection
