@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <x-admin.documents-by-category-role />
            </div>
            <div class="col-md-6">
                <x-admin.users-by-role />
            </div>
            <div class="col-md-6">
                <x-admin.opened-document-evolution />
            </div>
        </div>
    </div>
@endsection
