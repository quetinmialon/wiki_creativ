@extends('layouts.admin')

@section('content')

<div class="p-6 mt-12">
    <h1 class="text-xl font-semibold text-[#126C83] text-center mb-6">Liste des demandes d'utilisateurs en attente de validation utilisateur</h1>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full rounded-t-md">
            <thead>
                <tr class="bg-[#126C83] text-white">
                    <th class="px-6 py-3 text-center rounded-tl-md">Nom</th>
                    <th class="px-6 py-3 text-center">Email</th>
                    <th class="px-6 py-3 text-center ">Rôles à attribuer</th>
                    <th class="px-6 py-3 text-center rounded-tr-md">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($userRequests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="pl-8 pr-4 py-3 text-gray-900 font-medium">{{ $request->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $request->email }}</td>
                        <td class="px-6 py-3">
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex justify-center items-center space-x-2">
                                <form method="POST" action="{{ route('admin.requests.resendMail', $request->email) }}">
                                    @csrf
                                    <button  type = "submit">
                                        <img src="{{ asset('images/email.png') }}" alt="renvoyer un mail"/>
                                    </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">Aucune demande en attente de validation par l'utilisateur.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-8 flex justify-center">
        <a class="bg-[#126C83] text-white px-4 py-2 rounded hover:bg-[#126C83]" href="{{ route('admin.create-user') }}">
            Ajouter un utilisateur
        </a>
    </div>
    <a href="{{ url()->previous() }}" class="px-4 py-2 hover:text-white bg-white shadow-md rounded hover:bg-[#126C83] text-[#126C83]">
        Retour
    </a>
</div>
@endsection
