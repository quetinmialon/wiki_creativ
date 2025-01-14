<form method="POST" action="{{ route('subscribe.store') }}">
    @csrf
    <label for="name">Nom :</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required>

    <button type="submit">Envoyer la demande</button>
</form>

