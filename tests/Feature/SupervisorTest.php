<?php

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('redirects guests to login on index', function () {
    $this->get('/supervisor')
         ->assertRedirect(route('login'))
         ->assertSessionHas('error', 'Veuillez vous connecter pour accéder à cette page.');
});

it('blocks non-supervisor users on index', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get('/supervisor')
         ->assertRedirect(route('home'))
         ->assertSessionHas('error', 'accès non autorisé');
});

it('shows index with non-supervisor users', function () {
    // Création et attachement du superviseur
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13); // supervisor

    // Création d'un autre user
    $user = User::factory()->create();

    $this->actingAs($supervisor);

    $this->get('/supervisor')
         ->assertOk()
         ->assertViewIs('supervisor.index')
         ->assertViewHas('user', function ($collection) use ($user, $supervisor) {
             return $collection->contains('id', $user->id)
                 && ! $collection->contains('id', $supervisor->id);
         });
});

it('promotes a user to superadmin', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    $user = User::factory()->create();

    // Stub de Gate pour getSuperadminRole()
    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post("/supervisor/promote/{$user->id}")
         ->assertRedirect(route('supervisor.index'))
         ->assertSessionHas('success', 'Utilisateur promu au rôle de superadmin avec succès.');

    $this->assertTrue(
        $user->fresh()->roles->contains('id', 2),
        'Le rôle superadmin (id=2) doit être attaché.'
    );
});

it('revokes superadmin role from a user', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    $user = User::factory()->create();
    $user->roles()->attach(2);

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post("/supervisor/revokeRole/{$user->id}")
         ->assertRedirect(route('supervisor.index'))
         ->assertSessionHas('success', 'Rôle de superadmin retiré avec succès.');

    $this->assertFalse(
        $user->fresh()->roles->contains('id', 2),
        'Le rôle superadmin (id=2) doit être détaché.'
    );
});

it('soft-deletes a user', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    $user = User::factory()->create();

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post("/supervisor/revoke/{$user->id}")
         ->assertRedirect(route('supervisor.index'))
         ->assertSessionHas('success', 'Utilisateur supprimé avec succès.');

    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

it('lists revoked users', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    $user = User::factory()->create();
    $user->delete();

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->get('/supervisor/revokedUsers')
         ->assertOk()
         ->assertViewIs('supervisor.inactive_users_list')
         ->assertViewHas('users', function ($collection) use ($user) {
             return $collection->contains('id', $user->id);
         });
});

it('restores a revoked user', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    $user = User::factory()->create();
    $user->delete();

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post("/supervisor/revokedUsers/{$user->id}/restore")
         ->assertRedirect(route('supervisor.revokedUsers'))
         ->assertSessionHas('success', 'Utilisateur restauré avec succès.');

    $this->assertDatabaseHas('users', [
        'id'         => $user->id,
        'deleted_at' => null,
    ]);
});

it('sends superadmin invitation', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    // Mock du service
    $this->mock(\App\Services\SubscriptionService::class)
         ->shouldReceive('createSuperadminInvitation')
         ->once()
         ->with(['email' => 'inv@example.com'])
         ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post('/supervisor/createSuperadmin', ['email' => 'inv@example.com'])
         ->assertRedirect(route('supervisor.index'))
         ->assertSessionHas('success', 'Invitation envoyée avec succès.');
});

it('shows change-password form', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    $this->actingAs($supervisor);

    $this->get('/supervisor/changePassword')
         ->assertOk()
         ->assertViewIs('supervisor.change_password');
});

it('validates change-password payload', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post('/supervisor/changePassword', [])
         ->assertSessionHasErrors(['password']);
});

it('changes supervisor password successfully', function () {
    $supervisor = User::factory()->create();
    $supervisor->roles()->attach(13);

    Gate::shouldReceive('allows')
        ->with('supervisor', $supervisor)
        ->andReturnTrue();

    $this->actingAs($supervisor);

    $this->post('/supervisor/changePassword', [
        'password'              => 'newsecure123',
        'password_confirmation' => 'newsecure123',
    ])->assertRedirect(route('supervisor.index'))
      ->assertSessionHas('success', 'Mot de passe changé avec succès.');

    $this->assertTrue(
        Hash::check('newsecure123', $supervisor->fresh()->password),
        'Le mot de passe doit être mis à jour en base.'
    );
});
