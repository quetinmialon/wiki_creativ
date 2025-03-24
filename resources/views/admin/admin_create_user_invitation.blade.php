@extends('layouts.admin')

@section('content')
<form method="POST" action="{{ route('admin.create-user') }}">
    @csrf
    <label for="name">Nom :</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required>

    <label for="roles">Rôles :</label>
    <div id="roles">
        @foreach($roles as $role)
            <div>
                <input type="checkbox" id="role_{{ $role->id }}" name="role_ids[]" value="{{ $role->id }}">
                <label for="role_{{ $role->id }}">{{ $role->name }}</label>
            </div>
        @endforeach
    </div>

    <button type="submit">Créer l'utilisateur</button>
</form>
@endsection
