<?php

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use App\Models\Role;
use App\Models\User;
use App\Models\User\UserInvitation;
use App\Models\User\UserRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('create user request', function (): void {
    $data = ['name' => 'John Doe', 'email' => 'john.doe@example.com'];
    $this->post(route('subscribe.store'), $data);

    $this->assertDatabaseHas('user_requests', $data);
});

test('send user invitation', function (): void {
    Mail::fake();

    $userRequest = UserRequest::factory()->create(['status' => 'pending']);
    $this->assertDatabaseHas('user_requests', [
        'name' => $userRequest->name,
        'email' => $userRequest->email,
        'status' => $userRequest->status,
    ]);

    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $this->actingAs($user);

    $this->post(route('subscribe.process', $userRequest->id), [
        'action' => 'accept',
        'role_ids' => []
    ]);

    $this->assertDatabaseHas('user_invitations', ['email' => $userRequest->email]);

    Mail::assertSent(RegistrationLinkMail::class, fn($mail) => $mail->hasTo($userRequest->email));
});

test('send rejection mail', function (): void {
    Mail::fake();

    $userRequest = UserRequest::factory()->create(['status' => 'pending']);

    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $this->actingAs($user);

    $this->post(route('subscribe.process', $userRequest->id), ['action' => 'reject']);

    Mail::assertSent(RejectionMail::class, fn($mail) => $mail->hasTo($userRequest->email));
});

test('complete registration success', function (): void {
    $userRequest = UserRequest::factory()->create(['email' => 'jane.doe@example.com', 'status' => 'accepted']);
    $invitation = UserInvitation::factory()->create(['email' => $userRequest->email, 'token' => 'test_token']);

    $this->post(route('register.finalization', ['token' => 'test_token']), [
        'email' => $invitation->email,
        'password' => 'password123',
        'password_confirmation'=> 'password123'
    ]);

    $this->assertDatabaseHas('users', ['email' => $invitation->email]);
    $this->assertDatabaseMissing('user_invitations', ['email' => $invitation->email]);
});

test('choose password with invalid token', function (): void {
    $response = $this->get(route('register.finalization', ['token' => 'invalid_token']));
    $response->assertStatus(405);
});

test('choose password with valid token', function (): void {
    $invitation = UserInvitation::factory()->create(['email' => 'jane.doe@example.com', 'token' => 'valid_token']);
    $response = $this->get(route('register.complete', ['token' => 'valid_token']));

    $response->assertStatus(200);
    $response->assertViewIs('register.user_form_password_step');
    $response->assertViewHas('email', $invitation->email);
});

test('roles are associated to user invitation', function (): void {
    Mail::fake();

    $request = UserRequest::factory()->create(['email' => 'jane.doe@example.com']);
    $this->post(route('subscribe.store'), $request->only('name', 'email'));
    $this->assertDatabaseHas('user_requests', ['email' => $request->email]);

    $roles = Role::factory()->count(3)->create();
    $invitation = UserInvitation::factory()->create(['email' => $request->email]);
    $invitation->roles()->attach($roles->pluck('id'));

    $this->post(route('subscribe.process', $invitation->id), [
        'action' => 'accept',
        'role_ids' => $roles->pluck('id')->toArray(),
    ]);

    $this->assertDatabaseHas('user_invitation_role', [
        'user_invitation_id' => $invitation->id,
        'role_id' => $roles->first()->id,
    ]);

    expect($invitation->roles->pluck('name')->contains($roles->first()->name))->toBeTrue();
});

test('invitation roles transfer to user roles when user created', function (): void {
    Mail::fake();

    $request = UserRequest::factory()->create(['email' => 'jane.doe@example.com']);
    $roles = Role::factory()->count(3)->create();
    $invitation = UserInvitation::factory()->create(['email' => $request->email]);

    $invitation->roles()->attach($roles->pluck('id'));

    $this->post(route('subscribe.process', $invitation->id), [
        'action' => 'accept',
        'role_ids' => $roles->pluck('id')->toArray(),
    ]);

    $this->post(route('register.finalization', ['token' => $invitation->token]), [
        'email' => $invitation->email,
        'password' => 'password123',
        'password_confirmation'=> 'password123'
    ]);

    $user = User::where('email', $invitation->email)->first();
    $this->assertDatabaseHas('user_role', [
        'user_id' => $user->id,
        'role_id' => $roles->first()->id,
    ]);

    expect($user->roles->pluck('name')->contains($roles->first()->name))->toBeTrue();
});
