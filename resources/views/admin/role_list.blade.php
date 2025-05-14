@extends('layouts.admin')

@section('content')

<h2 class="text-xl font-semibold text-center text-[#126C83] mb-4">Liste des Rôles :</h2>

<div class="max-w-2xl mx-auto bg-white shadow-md rounded-md p-6">
    <table class="w-full rounded-t-md">
        <thead>
            <tr class="bg-[#126C83] text-white">
                <th class="px-4 py-2 rounded-tl-md">Nom du rôle</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                @if($role->name == 'supervisor')
                    @continue
                @endif
                @if(!Str::contains($role->name, 'Admin '))
                <tr>
                    <td class="pl-8 pr-4 py-2 font-medium text-gray-700">{{ $role->name }}</td>
                    <td class="px-4 py-2 flex flex-row space-x-2">
                        @if($role->name !== 'superadmin' && $role->name !== 'default')
                            <a href="{{ route('roles.edit', $role->id) }}">
                                <img src="{{ asset('images/edit.png') }}" alt="modifier le rôle" class="w-5 h-5"/>
                            </a>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce rôle ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    <img src="{{ asset('images/delete.png') }}" alt="supprimer le rôle" class="w-5 h-5"/>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<div class="flex justify-center mt-4">
    <a
        href="{{ route('roles.create') }}"
        class="bg-[#126C83] text-white px-4 py-2 rounded hover:bg-[#35A5A7] transition duration-200">
        Nouveau
    </a>
</div>

@endsection
