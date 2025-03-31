@extends('layouts.admin')

@section('content')
<x-search-bar.permission-search-bar/>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Liste des permissions en attente de traitement</h1>
        <a href="{{ route('admin.permissions') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Toutes les permissions</a>
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">Document</th>
                    <th class="border px-4 py-2">Commentaire</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Auteur</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td class="border px-4 py-2">{{ $permission->id }}</td>
                        <td class="border px-4 py-2">{{ $permission->document->name ?? 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $permission->comment?? 'Demande non commentée' }}</td>
                        <td class="border px-4 py-2">{{ $permission->status }}</td>
                        <td class="border px-4 py-2">{{ $permission->author }}</td>
                        <td class="border px-4 py-2">
                            <form action="{{ route('admin.permissions.handle', $permission->id) }}" method="POST">
                                @csrf
                                @method('POST')

                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    Accepter
                                </button>
                            </form>

                            <form action="{{ route('admin.permissions.handle', $permission->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('POST')

                                <input type="hidden" name="status" value="denied">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Rejeter
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
