@extends('layouts.admin')

@section('admin-contet')
<div>
    <x-user_request_list/>
    <a href="{{route('admin.register')}}">inviter un nouvel utilisateur</a>
</div>
@endsection
