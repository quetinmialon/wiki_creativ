<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('displays the login form to guests', function () {
    $this->get(route('login'))
         ->assertStatus(200)
         ->assertViewIs('auth.login');
});

test('allows user with valid credentials to login and redirect to home', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('redirects supervisor users to /supervisor after login', function () {
    $supervisorRole = Role::where('name', 'supervisor')->first();
    $user = User::factory()->create(['password' => bcrypt('password123')]);
    $user->roles()->attach($supervisorRole->id);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/supervisor');
    $this->assertAuthenticatedAs($user);
});

test('send error on login failure', function () {

    $response = $this->post(route('login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'wrong',
    ]);

    $response->assertSessionHasErrors();
});

test('logs out an authenticated user and redirects to login', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post('/logout')
         ->assertRedirect(route('login'))
         ->assertSessionHas('success', 'Déconnexion réussie.');
    $this->assertGuest();
});

test('displays forgot password form to guests', function () {
    $this->get(route('password.request'))
         ->assertStatus(200)
         ->assertViewIs('auth.forgot-password');
});

test('sends reset link when email exists', function () {
    Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'user@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

    $response = $this->post(route('password.email'), ['email' => 'user@example.com']);

    $response->assertSessionHas('status', __(
        Password::RESET_LINK_SENT
    ));
});

test('returns error when reset link sending fails', function () {
    Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn('error_code');

    $response = $this->post(route('password.email'), ['email' => 'bad@example.com']);

    $response->assertSessionHasErrors(['email']);
});

test('shows reset password form with token and email', function () {
    $token = 'dummy-token';

    $response = $this->get(route('password.reset', ['token' => $token, 'email' => 'user@example.com']));

    $response->assertStatus(200)
             ->assertViewIs('auth.reset-password')
             ->assertViewHasAll(['token', 'email']);
});

test('resets password successfully and redirects to login', function () {
    Password::shouldReceive('reset')
            ->once()
            ->with(
                ['email' => 'user@example.com', 'password' => 'newpassword', 'token' => 'token123'],

                // callback
                \Mockery::on(function ($callback) {
                    // simulate password reset callback invocation
                    $user = User::factory()->create(['email' => 'user@example.com']);
                    $callback($user, 'newpassword');
                    return true;
                })
            )
            ->andReturn(Password::PASSWORD_RESET);

    $response = $this->post(route('password.update'), [
        'email' => 'user@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
        'token' => 'token123',
    ]);

    $response->assertRedirect(route('login'))
             ->assertSessionHas('status', 'Password reset successful.');
});

test('returns error when reset token invalid', function () {
    Password::shouldReceive('reset')
            ->once()
            ->andReturn('invalid_token');

    $response = $this->post(route('password.update'), [
        'email' => 'user@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
        'token' => 'token123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('redirects guests away from protected routes using GuestMiddleware when logged in', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
         ->get(route('login'))
         ->assertRedirect(route('home'))
         ->assertSessionHas('error');
});

test('allows guests to access login when not authenticated', function () {
    $this->get(route('login'))->assertStatus(200);
});

test('redirects unauthenticated users from protected routes using AuthMiddleware', function () {
    // Assume a protected route named 'home' with AuthMiddleware
    $this->get(route('home'))
         ->assertRedirect(route('login'))
         ->assertSessionHas('error');
});

