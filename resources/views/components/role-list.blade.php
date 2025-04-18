<div class="max-w-2xl mx-auto mt-8 bg-white shadow-md rounded-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Liste des Rôles :</h2>
    <a
                        href="{{ route('roles.create') }}"
                        class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-400 transition duration-200">
                        Nouveau
                    </a>

    <ul class="divide-y divide-gray-200">
        @foreach ($roles as $role)
            @if($role->name == 'supervisor')
                @continue
            @endif
            @if(!Str::contains($role->name, 'Admin '))
            <li class="flex justify-between items-center py-3">
                <span class="text-gray-700 font-medium">{{ $role->name }}</span>
                @if($role->name !== 'superadmin' && $role->name !== 'default')
                <div class="flex space-x-2">
                    <!-- Bouton d'édition -->
                    <a
                        href="{{ route('roles.edit', $role->id) }}"
                        class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-400 transition duration-200">
                        Edit
                    </a>

                    <!-- Bouton de suppression -->
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce rôle ?');">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-400 transition duration-200">
                            Delete
                        </button>
                    </form>
                </div>
                @endif
            </li>
            @endif
        @endforeach
    </ul>
</div>

