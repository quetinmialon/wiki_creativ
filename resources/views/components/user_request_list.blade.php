@foreach($userRequests as $request)
    <div>
        <p>{{ $request->name }} - {{ $request->email }}</p>
        <form method="POST" action="{{ route('subscribe.process', $request->id) }}">
            @csrf
            
            <button name="action" value="accept">Accepter</button>
            <button name="action" value="reject">Refuser</button>
        </form>
    </div>
@endforeach
