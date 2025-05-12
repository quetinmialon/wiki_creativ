@extends('layouts.app')
@section('content')

<x-search-bar.document-search-bar/>

<div class="flex flex-row gap-6 divide-x divide-gray-200">
    <x-favorite-list/>
    <div class= "container flex flex-col gap-4 divide-y divide-gray-200">
        <x-last-opened-documents/>
        <div class="mb-6 ml-4">
            <h2 class="text-2xl text-[#126C83] m-4 text-center">
                Navigation Rapide
            </h2>
            <div class="flex flex-col">
                <a href="{{ route('profile.show') }}" class="text-[#126C83] underline hover:text-[#35A5A7]">
                    Gestion du profil
                </a>
                <a href="{{ route('create-documents') }}" class="text-[#126C83] underline hover:text-[#35A5A7]">
                    Créer un nouveau document
                </a>
                <a href="{{ route('credentials.index') }}" class="text-[#126C83] underline hover:text-[#35A5A7]">
                    Gestionnaire de mots de passes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

