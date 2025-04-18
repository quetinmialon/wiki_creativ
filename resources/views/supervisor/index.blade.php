@extends('layouts.supervisor')

@section('title', 'Liste des utilisateurs')

@section('content')
<h2 class="text-2xl font-semibold mb-4">Utilisateurs actifs</h2>
<form action ="{{ route('supervisor.createSuperadmin') }}" method='POST'>
    @csrf
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
        <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
    </div>
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
    </div>
    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 mb-4">Créer un superadmin</button>
</form>
<table class="w-full bg-white shadow rounded">
    <thead>
        <tr class="bg-gray-100 text-left">
            <th class="px-4 py-2">Nom</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Rôles</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($user as $u)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $u->name }}</td>
            <td class="px-4 py-2">{{ $u->email }}</td>
            <td class="px-4 py-2">
                {{ $u->roles->pluck('name')->join(', ') }}
            </td>
            <td class="px-4 py-2 space-x-2">
                @if (!$u->roles->contains('name', 'superadmin'))
                    <form action="{{ route('supervisor.promote', $u->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Promouvoir</button>
                    </form>
                @else
                    <form action="{{ route('supervisor.revokeRole', $u->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Retirer superadmin</button>
                    </form>
                @endif

                <form action="{{ route('supervisor.revoke', $u->id) }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Supprimer</button>
                </form>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
