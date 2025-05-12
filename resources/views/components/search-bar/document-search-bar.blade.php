<form method="POST" action="{{ route('documents.searchDocuments') }}" class="flex items-center p-4 bg-white shadow-md rounded-md">
    @csrf
    <div class="relative w-full">
        <input type="text" name="query" placeholder="Rechercher un document..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#126C83]"
            value="{{ old('query') }}" />

        @if(Str::contains(Route::currentRouteName(), 'admin.'))
            <input type='hidden' name="admin" value="admin" />
        @endif

        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-600 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l4-4m0 0l-4-4m4 4H7" />
            </svg>
        </button>
    </div>
</form>
