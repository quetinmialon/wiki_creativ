@extends('layouts.app')

@section('content')
<div>
    <x-user_request_list/>
    <a href="{{route('admin.register')}}">inviter un nouvel utilisateur</a>
</div>
@endsection
