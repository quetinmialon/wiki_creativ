@extends('layouts.admin')

@section('content')

<x-search-bar.user-search-bar/>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="w-full table-auto">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-gray-700 font-medium">Nom</th>
                <th class="px-6 py-3 text-left text-gray-700 font-medium">Email</th>
                <th class="px-6 py-3 text-left text-gray-700 font-medium">Rôles</th>
                <th class="px-6 py-3 text-left text-gray-700 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-900 font-medium">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($user->roles as $role)
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-lg">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.edit-users-role', $user->id) }}" class="text-blue-500 hover:text-blue-700">Modifier le role</a>
                        <form class="inline" action="{{ route('admin.delete-user', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Revoquer</button>
                        </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
