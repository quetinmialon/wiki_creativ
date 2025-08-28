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


test('subscribe view is returned successfully', function (): void {
    $response = $this->get(route('subscribe'));

    $response->assertStatus(200);
    $response->assertViewIs('register.user_form_first_step');
});


test('admin create user invitation form returns view with roles', function (): void {
    $roles = Role::factory()->count(2)->create();
    $admin = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'superadmin']);
    $admin->roles()->attach($adminRole);
    $this->actingAs($admin);
    $response = $this->get(route('admin.register'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.admin_create_user_invitation');
    $response->assertViewHas('roles');
});

test('admin can create a user invitation', function (): void {
    Mail::fake();
    $roles = Role::factory()->count(2)->create();
    $admin = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'superadmin']);
    $admin->roles()->attach($adminRole);
    $this->actingAs($admin);

    $response = $this->post(route('admin.create-user'), [
        'name' => 'Test User',
        'email' => 'newuser@example.com',
        'role_ids' => $roles->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('admin'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('user_invitations', ['email' => 'newuser@example.com']);
});

test('create user invitation fails if email exists in users', function (): void {
    User::factory()->create(['email' => 'exists@example.com']);
    $roles = Role::factory()->count(1)->create();

    $admin = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'superadmin']);
    $admin->roles()->attach($adminRole);
    $this->actingAs($admin);

    $response = $this->post(route('admin.create-user'), [
        'name' => 'Dup',
        'email' => 'exists@example.com',
        'role_ids' => $roles->pluck('id')->toArray(),
    ]);

    $response->assertSessionHasErrors(['email']);
});
