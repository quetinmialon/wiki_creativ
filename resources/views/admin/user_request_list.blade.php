@extends('layouts.admin')

@section('content')

<a class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300" href="{{ route('admin.create-user') }}">
    Ajouter un utilisateur
</a>

<x-user-request-list/>

@endsection
