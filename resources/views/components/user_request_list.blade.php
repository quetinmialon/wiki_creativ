@foreach($userRequests as $request)
    <div class="max-w-2xl mx-auto mt-8 bg-white shadow-md rounded-md p-6">
        <p class="text-lg font-semibold text-gray-800">{{ $request->name }}</p>
        <p class="text-gray-600">{{ $request->email }}</p>

        <form method="POST" action="{{ route('subscribe.process', $request->id) }}" class="mt-4">
            @csrf
            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-2">Rôles :</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                        <div class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                name="role_ids[]"
                                value="{{ $role->id }}"
                                id="role_{{ $role->id }}"
                                class="text-blue-500 border-gray-300 focus:ring-blue-400 rounded">
                            <label for="role_{{ $role->id }}" class="text-gray-700">{{ $role->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex space-x-4">
                <button name="action" value="accept"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    Accepter
                </button>
                <button name="action" value="reject"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    Refuser
                </button>
            </div>
        </form>
    </div>
@endforeach

