@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Modifier les rôles de l'utilisateur</h2>

        <form action="{{ route('admin.update-user-roles', ['id' => $user->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Champ caché pour l'ID -->
            <input type="hidden" name="id" value="{{ $user->id }}">

            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" id="name" class="form-control" value="{{ $user->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Rôles</label>
                <div class="form-check">
                    @foreach($roles as $role)
                    @if($role->name == 'supervisor')
                        @continue
                    @endif
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="role_{{ $role->id }}"
                            name="roles[]"
                            value="{{ $role->id }}"
                            {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                        <br>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
@endsection

