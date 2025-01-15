<?php

namespace Tests\Feature;

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use App\Models\User\UserRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions;

    public function test_create_user_request()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];
        $this->post(route('subscribe.store'), $data);

        $this->assertDatabaseHas('user_requests', $data);

    }

    public function test_send_user_invitation()
    {
        //mocking mail system
        Mail::fake();
        // test creating user_requests
        $userRequest = UserRequest::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ]);
        $expected = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ];
        $this->assertDatabaseHas('user_requests',$expected);

        // test validating user_request and creating user_invitation

        $this->post(route('subscribe.process', $userRequest->id), ['action' => 'accept']);
        $this->assertDatabaseHas('user_invitations', ['email' => 'john.doe@example.com']);

        // test if the mail has been sent successfully
        Mail::assertSent(RegistrationLinkMail::class, function ($mail) use ($userRequest) {
            return $mail->hasTo($userRequest->email);
        });
    }
    public function test_send_rejection_mail(){
        //mocking mail system
        Mail::fake();
        // test creating user_requests
        $userRequest = UserRequest::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ]);
        $expected = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ];
        $this->assertDatabaseHas('user_requests',$expected);

        // test validating user_request and creating user_invitation
        $this->post(route('subscribe.process', $userRequest->id), ['action' => 'reject']);

        // test if the mail has been sent successfully
        Mail::assertSent(RejectionMail::class, function ($mail) use ($userRequest) {
            return $mail->hasTo($userRequest->email);
        });


    }
    public function test_complete_registration_success()
    {

        $userRequest = UserRequest::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'status'=>'accepted'
        ]);

        DB::table('user_invitations')->insert([
            'email' => 'jane.doe@example.com',
            'token' => 'test_token',
            'created_at' => now(),
        ]);

        $this->post(route('register.finalization', ['token' =>'test_token']), [
            'email' => 'jane.doe@example.com',
            'password' => 'password123',
        ],);

        $this->assertDatabaseHas('users', [
            'email' => 'jane.doe@example.com',
        ]);

        $this->assertDatabaseMissing('user_invitations', [
            'email' => 'jane.doe@example.com',
        ]);
    }

    public function test_choose_password_with_invalid_token()
    {
        $response = $this->get(route('register.finalization', ['token' => 'invalid_token']));

        $response->assertStatus(405);
    }

    public function test_choose_password_with_valid_token()
    {
        DB::table('user_invitations')->insert([
            'email' => 'jane.doe@example.com',
            'token' => 'valid_token',
            'created_at' => now(),
        ]);

        $response = $this->get(route('register.complete', ['token' => 'valid_token']));

        $response->assertStatus(200);
        $response->assertViewIs('user_form_password_step');
        $response->assertViewHas('email', 'jane.doe@example.com');
    }
}
