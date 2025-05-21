@extends('layouts.admin')

@section('content')
    <x-search-bar.permission-search-bar/>
    <div class="container mx-auto p-6">
        <h1 class="text-xl font-semibold text-[#126C83] text-center mb-6">Liste des permissions</h1>
        <a href="{{ route('admin.permissions.pendings') }}" class="bg-[#126C83] text-white px-4 py-2 rounded hover:bg-[#35A5A7] mb-4 inline-block">Permissions en attente</a>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full rounded-t-md">
                <thead>
                    <tr class="bg-[#126C83] text-white">
                        <th class="px-6 py-3 text-left rounded-tl-md">#</th>
                        <th class="px-6 py-3 text-left">Document</th>
                        <th class="px-6 py-3 text-left">Commentaire</th>
                        <th class="px-6 py-3 text-left">Statut</th>
                        <th class="px-6 py-3 text-left">Auteur</th>
                        <th class="px-6 py-3 text-left rounded-tr-md">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($permissions as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="pl-8 pr-4 py-3 text-gray-900 font-medium">{{ $permission->id }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $permission->document->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $permission->comment ?? 'Demande non commentée' }}</td>
                            <td class="px-6 py-3">
                                @if($permission->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-lg">En attente</span>
                                @elseif($permission->status == 'approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-lg">Approuvée</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-lg">Refusée</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-700">{{ App\Models\User::find($permission->author)->name ?? 'utilisateur supprimé'}}</td>
                            <td class="px-6 py-3 flex flex-col space-y-2">
                                @if($permission->status == 'pending')
                                    <form action="{{ route('admin.permissions.handle', $permission->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full">
                                            Accepter
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.permissions.handle', $permission->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="denied">
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full">
                                            Rejeter
                                        </button>
                                    </form>
                                @elseif($permission->status == 'approved')
                                    <div class="text-xs text-gray-500 mb-1">Expire le : {{ $permission->expired_at }}</div>
                                    <form action="{{ route('admin.permissions.handle', $permission->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="denied">

                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full">
                                            Révoquer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
