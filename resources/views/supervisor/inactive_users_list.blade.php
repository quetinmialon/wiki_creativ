@extends('layouts.supervisor')

@section('title', 'Utilisateurs supprimés')

@section('content')
<h2 class="text-2xl font-semibold mb-4">Utilisateurs supprimés</h2>

<table class="w-full bg-white shadow rounded">
    <thead>
        <tr class="bg-gray-100 text-left">
            <th class="px-4 py-2">Nom</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $user->name }}</td>
            <td class="px-4 py-2">{{ $user->email }}</td>
            <td class="px-4 py-2">
                <form action="{{ route('supervisor.restoreUser', $user->id) }}" method="POST">
                    @csrf
                    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Restaurer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

