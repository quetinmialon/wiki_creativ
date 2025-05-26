<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    // Création d'un utilisateur avec mot de passe connu
    $this->user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);
});

test('redirects guests from profile routes to login', function (): void {
    $this->get('/profile')->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Vous devez être connecté pour accéder à cette page.');

    $this->get('/profile/edit')->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Vous devez être connecté pour accéder à cette page.');

    $this->get('/profile/change-password')->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Vous devez être connecté pour accéder à cette page.');

    $this->put('/profile', [
        'name' => 'Test',
        'email' => 'test@example.com',
    ])->assertRedirect(route('login'))
      ->assertSessionHas('error', 'Vous devez être connecté pour accéder à cette page.');

    $this->post('/profile/change-password', [
        'current_password' => 'foo',
        'new_password' => 'bar',
        'new_password_confirmation' => 'bar',
    ])->assertRedirect(route('login'))
      ->assertSessionHas('error', 'Vous devez être connecté pour accéder à cette page.');
});

test('shows profile and edit forms to authenticated users', function (): void {
    $this->actingAs($this->user);

    $this->get('/profile')
        ->assertOk()
        ->assertViewIs('user.profile')
        ->assertViewHas('user', fn($u) => $u->id === $this->user->id);

    $this->get('/profile/edit')
        ->assertOk()
        ->assertViewIs('user.edit-profile')
        ->assertViewHas('user', fn($u) => $u->id === $this->user->id);
});

test('returns 404 when authenticated user has no record', function (): void {
    $this->actingAs($this->user);
    $this->user->delete(); // simule absence en base

    $this->get('/profile')->assertNotFound();
    $this->get('/profile/edit')->assertNotFound();
});

test('updates profile with valid data', function (): void {
    $this->actingAs($this->user);

    $this->put('/profile', [
        'name' => 'Nouveau Nom',
        'email' => 'nouveau@example.com',
    ])->assertRedirect(route('profile.show'))
      ->assertSessionHas('success', 'Profil mis à jour avec succès.');

    $this->assertDatabaseHas('users', [
        'id'    => $this->user->id,
        'name'  => 'Nouveau Nom',
        'email' => 'nouveau@example.com',
    ]);
});

test('fails update profile on validation errors', function (): void {
    $this->actingAs($this->user);

    // nom manquant
    $this->put('/profile', [
        'name'  => '',
        'email' => 'valid@example.com',
    ])->assertSessionHasErrors('name');

    // email invalide
    $this->put('/profile', [
        'name'  => 'Test',
        'email' => 'invalid-email',
    ])->assertSessionHasErrors('email');

    // email en doublon
    User::factory()->create(['email' => 'exist@example.com']);
    $this->put('/profile', [
        'name'  => 'Test',
        'email' => 'exist@example.com',
    ])->assertSessionHasErrors('email');
});

test('shows change password form', function (): void {
    $this->actingAs($this->user);

    $this->get('/profile/change-password')
        ->assertOk()
        ->assertViewIs('user.change-password');
});

test('fails change password on validation', function (): void {
    $this->actingAs($this->user);

    // champs manquants
    $this->post('/profile/change-password', [])->assertSessionHasErrors(['current_password','new_password']);

    // confirmation mismatch
    $this->post('/profile/change-password', [
        'current_password'          => 'oldpassword',
        'new_password'              => 'newpass123',
        'new_password_confirmation' => 'different',
    ])->assertSessionHasErrors('new_password');
});

test('fails change password when current is incorrect', function (): void {
    $this->actingAs($this->user);

    $this->post('/profile/change-password', [
        'current_password'          => 'wrongpassword',
        'new_password'              => 'newpass123',
        'new_password_confirmation' => 'newpass123',
    ])
    ->assertRedirect()
    ->assertSessionHas('error', 'Mot de passe actuel incorrect.');
});

test('changes password successfully', function (): void {
    $this->actingAs($this->user);

    $this->post('/profile/change-password', [
        'current_password'          => 'oldpassword',
        'new_password'              => 'newpass123',
        'new_password_confirmation' => 'newpass123',
    ])->assertRedirect(route('profile.show'))
      ->assertSessionHas('success', 'Mot de passe changé avec succès.');

    $this->assertTrue(
        Hash::check('newpass123', $this->user->fresh()->password),
        'Le mot de passe en base doit être mis à jour'
    );
});
