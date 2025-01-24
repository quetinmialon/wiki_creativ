@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Liste des permissions</h1>
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">Document</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Auteur</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td class="border px-4 py-2">{{ $permission->id }}</td>
                        <td class="border px-4 py-2">{{ $permission->document->title ?? 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $permission->status }}</td>
                        <td class="border px-4 py-2">{{ $permission->author }}</td>
                        <td class="border px-4 py-2">
                            <a href="#" class="text-blue-500 hover:underline">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
