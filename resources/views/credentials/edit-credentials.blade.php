@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10 px-6">
    <h1 class="text-xl font-semibold text-center text-[#126C83] mb-6">Modifier le compte dans le gestionnaire de mot de passe</h1>
    <form action="{{ route('credentials.update', $credential->id) }}" method="POST" class="space-y-6 bg-white p-8 shadow-md rounded-md">
        @csrf
        @method('PUT')
        <div>
            <label for="destination" class="block text-gray-700 font-medium mb-2">Destination</label>
            <input type="text" name="destination" id="destination" class="w-full px-4 py-2 border border-gray-300 rounded-md" value="{{ $credential->destination }}" required>
        </div>
        <div>
            <label for="username" class="block text-gray-700 font-medium mb-2">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" class="w-full px-4 py-2 border border-gray-300 rounded-md" value="{{ $credential->username }}" required>
        </div>
        <div>
            <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
            <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-md" value="{{ $credential->password }}" required>
        </div>
            <!-- Switch public/privé -->
        <div class="flex items-center my-4">
            <label for="password_public" class="mr-2 text-sm font-medium text-gray-700">Partager l'identifiant ?</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    id="password_public"
                    name="is_public"
                    value="1"
                    class="sr-only peer"
                    {{ $credential->roleList ? 'checked' : '' }}
                >
                <div class="w-11 h-6 bg-gray-600 rounded-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#35A5A7] peer peer-checked:bg-[#35A5A7] after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
            </label>
        </div>

        <!-- Select rôle -->
        <div id="roleSelectDiv" class="mb-6" style="{{ $credential->roleList ? 'display: none ' : '' }}">
            <label for="role_id" class="block text-gray-700 font-medium mb-2">Partager avec un rôle</label>
            <select name="role_id" id="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                <option value="" {{ $credential->roleList ? 'selected' : '' }} hidden>Aucun</option>
                @foreach($roleList as $role)
                    @if(!str_contains($role->name, 'Admin '))
                        <option
                            value="{{ $role->id }}"
                            @if($credential->roleList)
                                {{ $credential->roleList->contains($role->id) ? 'selected' : '' }}
                            @endif
                        >
                            {{ $role->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <button type="submit" class="px-6 py-2 bg-[#35A5A7] text-white font-semibold rounded-md shadow hover:bg-[#126C83]">
            Mettre à jour
        </button>
    </form>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const switchCheckbox = document.getElementById('password_public');
    const roleSelectDiv = document.getElementById('roleSelectDiv');
    const roleSelect = document.getElementById('role_id');

    const toggleRoleVisibility = () => {
        if (switchCheckbox.checked) {
            roleSelectDiv.style.display = 'block';
        } else {
            roleSelectDiv.style.display = 'none';
            roleSelect.value = "";
        }
    };

    toggleRoleVisibility(); // initialisation correcte même à l’édition

    switchCheckbox.addEventListener('change', toggleRoleVisibility);
});
</script>


