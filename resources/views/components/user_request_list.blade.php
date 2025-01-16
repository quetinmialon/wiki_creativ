@foreach($userRequests as $request)
    <div>
        <p>{{ $request->name }} - {{ $request->email }}</p>
        <form method="POST" action="{{ route('subscribe.process', $request->id) }}">
            @csrf
            <div>
                <label>Rôles :</label>
                @foreach($roles as $role)
                    <div>
                        <input
                            type="checkbox"
                            name="role_ids[]"
                            value="{{ $role->id }}"
                            id="role_{{ $role->id }}">
                        <label for="role_{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            <button name="action" value="accept">Accepter</button>
            <button name="action" value="reject">Refuser</button>
        </form>
    </div>
@endforeach
