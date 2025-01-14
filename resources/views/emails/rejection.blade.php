@component('mail::message')
# Demande d'inscription refusée

Nous sommes désolés, mais votre demande d'inscription a été refusée.

**Raison :**
{{ $reason }}

Merci de votre compréhension,
L'équipe {{ config('app.name') }}
@endcomponent

