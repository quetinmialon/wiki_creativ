@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-xl font-semibold text-center text-[#126C83] mb-4">Demande de permission</h1>
        <form action="{{ route('permissions.create') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="document_id" value="{{ $document->id }}">

            <div>
                <label for="expired_at" class="block text-sm font-medium text-gray-700">Date d'expiration</label>
                <input type="date" id="expired_at" name="expired_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label for="comment" class="block text-sm font-medium text-gray-700">Commentaire</label>
                <textarea id="comment" name="comment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <div class="flex justify-center">
                <button type="submit" class="bg-[#35A5A7] text-white px-4 py-2 rounded-md hover:text-[#126C83]">Envoyer</button>
            </div>
        </form>
    </div>
@endsection
