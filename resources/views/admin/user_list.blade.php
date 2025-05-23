@extends('layouts.admin')

@section('content')

<x-search-bar.user-search-bar/>

<div class="container mx-auto p-6">
    <h1 class="text-xl font-semibold text-[#126C83] text-center mb-6">Liste des utilisateurs</h1>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full rounded-t-md">
            <thead>
                <tr class="bg-[#126C83] text-white">
                    <th class="px-6 py-3 text-left rounded-tl-md">Nom</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Rôles</th>
                    <th class="px-6 py-3 text-left rounded-tr-md">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="pl-8 pr-4 py-3 text-gray-900 font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($user->roles as $role)
                                    @if(collect(['supervisor', 'default'])->contains($role->name))
                                        @continue
                                    @endif
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-lg">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-3 flex flex-row items-center space-x-2">
                            <a href="{{ route('admin.edit-users-role', $user->id) }}" class="text-[#126C83] underline hover:text-[#35A5A7]">
                                <img src="{{ asset('images/edit.png') }}" alt="Modifier l\'utilisateur" class="inline w-5 h-5"/>
                            </a>
                            <form class="inline-block" action="{{ route('admin.delete-user', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Êtes-vous sûr de vouloir révoquer l\'utilisateur suivant : {{ addslashes($user->name) }} ?')">
                                    <img src="{{ asset('images/delete.png') }}" alt="Révoquer l\'utilisateur" class="inline w-5 h-5"/>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-8 flex justify-center">
        <a class="bg-[#126C83] text-white px-4 py-2 rounded hover:bg-[#126C83]" href="{{ route('admin.create-user') }}">
            Ajouter un utilisateur
        </a>
    </div>
</div>
<div class="mt-6">
    {{ $users->links() }}
</div>
@endsection
