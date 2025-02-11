@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('register.finalization') }}">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    <input type="hidden" name="token" value="{{ $token }}">
    <label for="password">Mot de passe :</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Finaliser l'inscription</button>
</form>
@endsection
