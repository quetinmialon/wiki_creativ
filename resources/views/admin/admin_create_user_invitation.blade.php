@extends('layouts.admin')

@section('content')
<div class="container mx-auto mt-4">
    <h2 class="text-xl font-semibold text-center text-[#126C83] mb-4">Inviter un nouvel utilisateur</h2>

    <form method="POST" action="{{ route('admin.create-user') }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Nom de l'utilisateur"
            >
            @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Adresse email"
            >
            @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-4">Rôles</label>

            <div class="space-y-3">
                @foreach($roles as $role)
                    @if($role->name === 'supervisor')
                        @continue
                    @endif
                    @if(!str_contains($role->name, 'Admin ') && $role->name !== 'default')
                        @php
                            $adminRole = $roles->firstWhere('name', 'Admin ' . $role->name);
                        @endphp
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded shadow-sm">
                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    name="role_ids[]"
                                    value="{{ $role->id }}"
                                    id="role_{{ $role->id }}"
                                    class="role-checkbox text-blue-500 border-gray-300 focus:ring-blue-400 rounded"
                                    data-role="{{ $role->id }}"
                                    data-admin-role="{{ $adminRole ? $adminRole->id : '' }}"
                                >
                                <label for="role_{{ $role->id }}" class="text-gray-700">{{ ucfirst($role->name) }}</label>
                            </div>

                            @if($adminRole)
                                <div class="flex items-center space-x-2">
                                    <input
                                        type="checkbox"
                                        name="role_ids[]"
                                        value="{{ $adminRole->id }}"
                                        id="admin_switch_{{ $role->id }}"
                                        class="admin-switch w-5 h-5 text-red-500 border-gray-300 focus:ring-red-400 rounded"
                                    >
                                    <label for="admin_switch_{{ $role->id }}" class="text-red-700 font-semibold">Admin</label>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
                <input type="checkbox" class="hidden" value="1" checked name="roles[]">
            </div>
        </div>

        <div class="flex items-center justify-center">
            <button type="submit" class="bg-[#35A5A7] text-white px-4 py-2 rounded hover:bg-[#126C83]">
                Créer l'utilisateur
            </button>
        </div>
    </form>
</div>
@endsection
