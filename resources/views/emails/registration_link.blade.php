@component('mail::message')
# Bienvenue !

Cliquez sur le bouton ci-dessous pour finaliser votre inscription :

@component('mail::button', ['url' => $link])
Finaliser l'inscription
@endcomponent

Merci,
L'équipe {{ config('app.name') }}
@endcomponent

