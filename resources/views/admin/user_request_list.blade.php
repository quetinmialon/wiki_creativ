@extends('layouts.admin')

@section('content')

<a class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300" href="{{ route('admin.create-user') }}">
    Ajouter un utilisateur
</a>

@foreach($userRequests as $request)
    <div class="max-w-2xl mx-auto mt-8 bg-white shadow-md rounded-md p-6">
        <p class="text-lg font-semibold text-gray-800">{{ $request->name }}</p>
        <p class="text-gray-600">{{ $request->email }}</p>

        <form method="POST" action="{{ route('subscribe.process', $request->id) }}" class="mt-4">
            @csrf

            {{-- Sélection des rôles --}}
            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-2">Rôles :</label>

                {{-- Rôle Default (coché et non modifiable) --}}
                <div class="flex items-center space-x-2">
                    <input type="hidden" name="role_ids[]" value="1" id="role_default">
                </div>

                {{-- Liste des rôles classiques avec option Admin --}}
                <div class="grid grid-cols-2 gap-2 mt-2">
                    @foreach($roles as $role)
                    @if($role->name =='supervisor')
                        @continue
                    @endif
                        @if(!str_contains($role->name, 'Admin ') && $role->name !== 'default')
                            @php
                                $adminRole = $roles->firstWhere('name', 'Admin ' . $role->name);
                            @endphp
                            <div class="flex items-center justify-between bg-gray-100 p-2 rounded">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                        id="role_{{ $role->id }}" class="role-checkbox text-blue-500 border-gray-300 focus:ring-blue-400 rounded"
                                        data-role="{{ $role->id }}" data-admin-role="{{ $adminRole ? $adminRole->id : '' }}">
                                    <label for="role_{{ $role->id }}" class="text-gray-700">{{ ucfirst($role->name) }}</label>
                                </div>

                                {{-- Switch pour attribuer le rôle Admin --}}
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
            {{-- Boutons d'action --}}
            <div class="flex space-x-4 mt-4">
                <button name="action" value="accept"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    Accepter
                </button>
                <button name="action" value="reject"
                    class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    Refuser
                </button>
            </div>
        </form>

    </div>
@endforeach



@endsection
