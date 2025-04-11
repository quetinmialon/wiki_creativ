<?php

namespace Tests\Feature;

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use App\Models\Role;
use App\Models\User;
use App\Models\User\UserInvitation;
use App\Models\User\UserRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_create_user_request()
    {
        $data = ['name' => 'John Doe', 'email' => 'john.doe@example.com'];
        $this->post(route('subscribe.store'), $data);

        $this->assertDatabaseHas('user_requests', $data);
    }

    public function test_send_user_invitation()
    {
        Mail::fake();

        $userRequest = UserRequest::factory()->create(['status' => 'pending']);
        $this->assertDatabaseHas('user_requests', [
            'name' => $userRequest->name,
            'email' => $userRequest->email,
            'status' => $userRequest->status,
        ]);

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        $this->post(route('subscribe.process', $userRequest->id), [
            'action' => 'accept',
            'role_ids' => []
        ]);


        $this->assertDatabaseHas('user_invitations', ['email' => $userRequest->email]);

        Mail::assertSent(RegistrationLinkMail::class, fn($mail) => $mail->hasTo($userRequest->email));
    }

    public function test_send_rejection_mail()
    {
        Mail::fake();

        $userRequest = UserRequest::factory()->create(['status' => 'pending']);

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);
        
        $this->post(route('subscribe.process', $userRequest->id), ['action' => 'reject']);

        Mail::assertSent(RejectionMail::class, fn($mail) => $mail->hasTo($userRequest->email));
    }

    public function test_complete_registration_success()
    {
        $userRequest = UserRequest::factory()->create(['email' => 'jane.doe@example.com', 'status' => 'accepted']);
        $invitation = UserInvitation::factory()->create(['email' => $userRequest->email, 'token' => 'test_token']);

        $this->post(route('register.finalization', ['token' => 'test_token']), [
            'email' => $invitation->email,
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => $invitation->email]);
        $this->assertDatabaseMissing('user_invitations', ['email' => $invitation->email]);
    }

    public function test_choose_password_with_invalid_token()
    {
        $response = $this->get(route('register.finalization', ['token' => 'invalid_token']));
        $response->assertStatus(405);
    }

    public function test_choose_password_with_valid_token()
    {
        $invitation = UserInvitation::factory()->create(['email' => 'jane.doe@example.com', 'token' => 'valid_token']);
        $response = $this->get(route('register.complete', ['token' => 'valid_token']));

        $response->assertStatus(200);
        $response->assertViewIs('user_form_password_step');
        $response->assertViewHas('email', $invitation->email);
    }

    public function test_roles_are_associated_to_user_invitation()
    {
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

        $this->assertTrue($invitation->roles->pluck('name')->contains($roles->first()->name));
    }

    public function test_invitation_roles_transfer_to_user_roles_when_user_created()
    {
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
        ]);

        $user = User::where('email', $invitation->email)->first();
        $this->assertDatabaseHas('user_role', [
            'user_id' => $user->id,
            'role_id' => $roles->first()->id,
        ]);

        $this->assertTrue($user->roles->pluck('name')->contains($roles->first()->name));
    }
}

