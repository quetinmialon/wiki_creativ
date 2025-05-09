<?php

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

beforeAll(function () {
    Route::middleware([])->group(function () {
        Route::get('/register/{token}', fn () => 'ok')->name('register.complete');
    });
});

test('builds RegistrationLinkMail with correct subject and link in rendered output', function () {
    $token = 'abcdef123456';
    $mailable = new RegistrationLinkMail($token);
    $mailable->build();
    expect($mailable->subject)
        ->toBe('Finalisez votre inscription au wiki creative');
    $expectedUrl = URL::route('register.complete', ['token' => $token]);
    expect($mailable->link)
        ->toBe($expectedUrl);
    $rendered = $mailable->render();
    expect($rendered)
        ->toContain($expectedUrl);
});

test('builds RejectionMail with default reason and contains it in rendered output', function () {
    $mailable = new RejectionMail();
    $mailable->build();
    expect($mailable->subject)
        ->toBe('Inscription au wiki rejetée');
    expect($mailable->reason)
        ->toBe('Nous ne pouvons pas donner suite à votre demande pour le moment.');
    $rendered = $mailable->render();
    expect($rendered)
        ->toContain($mailable->reason);
});

test('builds RejectionMail with custom reason and contains it in rendered output', function () {
    $custom = 'Motif spécifique du rejet.';
    $mailable = new RejectionMail($custom);
    $mailable->build();
    expect($mailable->reason)
        ->toBe($custom);
    $rendered = $mailable->render();
    expect($rendered)
        ->toContain($custom);
});
