@extends('layouts.admin')

@section('content')

<div class="p-6 mt-12">
    <h1 class="text-xl font-semibold text-[#126C83] text-center mb-6">Liste des demandes d'utilisateurs</h1>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full rounded-t-md">
            <thead>
                <tr class="bg-[#126C83] text-white">
                    <th class="px-6 py-3 text-center rounded-tl-md">Nom</th>
                    <th class="px-6 py-3 text-center">Email</th>
                    <th class="px-6 py-3 text-center ">Rôles à attribuer</th>
                    <th class="px-6 py-3 text-center rounded-tr-md">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($userRequests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="pl-8 pr-4 py-3 text-gray-900 font-medium">{{ $request->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $request->email }}</td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('subscribe.process', $request->id) }}">
                                @csrf
                                <div class="flex flex-col gap-2">
                                    <input type="hidden" name="role_ids[]" value="1" id="role_default">
                                    <div class="flex flex-col gap-2">
                                        @foreach($roles as $role)
                                            @if($role->name == 'supervisor')
                                                @continue
                                            @endif
                                            @if(!str_contains($role->name, 'Admin ') && $role->name !== 'default')
                                                @php
                                                    $adminRole = $roles->firstWhere('name', 'Admin ' . $role->name);
                                                @endphp
                                                <div class="flex items-center justify-between p-0 rounded">
                                                    <div class="flex items-center space-x-2">
                                                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                                            id="role_{{ $role->id }}" class="role-checkbox text-blue-500 border-gray-300 focus:ring-blue-400 rounded"
                                                            data-role="{{ $role->id }}" data-admin-role="{{ $adminRole ? $adminRole->id : '' }}">
                                                        <label for="role_{{ $role->id }}" class="text-gray-700">{{ ucfirst($role->name) }}</label>
                                                    </div>
                                                    @if($adminRole)
                                                        <div class="flex items-center space-x-2">
                                                            <input type="checkbox" class="admin-switch w-5 h-5 text-red-500 border-gray-300 focus:ring-red-400 rounded"
                                                                id="admin_switch_{{ $role->id }}" data-admin-role="{{ $adminRole->id }}">
                                                            <label for="admin_switch_{{ $role->id }}" class="text-red-700 font-semibold">Admin</label>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex justify-center items-center space-x-2">
                                <button name="action" value="accept">
                                    <img src="{{ asset('images/accept.png') }}"/>
                                </button>
                                <button name="action" value="reject">
                                    <img src="{{ asset('images/denie.png') }}"/>
                                </button>
                            </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">Aucune demande en attente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-8 flex justify-center">
        <a class="bg-[#126C83] text-white px-4 py-2 rounded hover:bg-[#126C83]" href="{{ route('admin.create-user') }}">
            Ajouter un utilisateur
        </a>
    </div>
</div>
@endsection
