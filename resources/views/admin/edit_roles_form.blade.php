@extends('layouts.admin')

@section('content')
<div class="container mx-auto mt-10">
    <h2 class="text-xl font-semibold text-center text-[#126C83] mb-6">Modifier les rôles de l'utilisateur</h2>

    <form method="POST" action="{{ route('admin.update-user-roles', ['id' => $user->id]) }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <input type="hidden" name="id" value="{{ $user->id }}">

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
            <input
                type="text"
                id="name"
                value="{{ $user->name }}"
                readonly
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-600 bg-gray-100"
            >
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input
                type="email"
                id="email"
                value="{{ $user->email }}"
                readonly
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-600 bg-gray-100"
            >
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-4">Rôles</label>

            <div class="space-y-3">
                @foreach($roles as $role)
                    @if(in_array($role->name, ['supervisor', 'default']))
                        @continue
                    @endif
                    @if(!str_starts_with($role->name, 'Admin '))
                        @php
                            $adminRole = $roles->firstWhere('name', 'Admin ' . $role->name);
                            $hasRole = $user->roles->contains($role->id);
                            $hasAdminRole = $adminRole ? $user->roles->contains($adminRole->id) : false;
                        @endphp

                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded shadow-sm">
                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->id }}"
                                    id="role_{{ $role->id }}"
                                    class="role-checkbox text-blue-500 border-gray-300 focus:ring-blue-400 rounded"
                                    data-role="{{ $role->id }}"
                                    data-admin-role="{{ $adminRole ? $adminRole->id : '' }}"
                                    {{ $hasRole ? 'checked' : '' }}
                                >
                                <label for="role_{{ $role->id }}" class="text-gray-700">{{ ucfirst($role->name) }}</label>
                            </div>

                            @if($adminRole)
                                <div class="flex items-center space-x-2">
                                    <input
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $adminRole->id }}"
                                        id="admin_switch_{{ $role->id }}"
                                        class="admin-switch w-5 h-5 text-red-500 border-gray-300 focus:ring-red-400 rounded"
                                        {{ $hasAdminRole ? 'checked' : '' }}
                                    >
                                    <label for="admin_switch_{{ $role->id }}" class="text-red-700 font-semibold">Admin</label>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach

                <!-- rôle hidden toujours coché -->
                <input type="checkbox" class="hidden" value="1" checked name="roles[]">
            </div>
        </div>

        <div class="flex items-center justify-center">
            <button type="submit" class="bg-[#35A5A7] text-white px-4 py-2 rounded hover:bg-[#126C83]">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
