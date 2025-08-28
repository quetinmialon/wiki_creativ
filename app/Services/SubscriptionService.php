<?php

namespace App\Services;

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use App\Models\User;
use App\Models\User\UserInvitation;
use App\Models\User\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;

class SubscriptionService
{
    public function createUserRequest(array $data)
    {
        UserRequest::where('email', $data['email'])->delete(); // Remove any existing request with the same email
        if ( UserInvitation::where('email', $data['email'])->exists()|| User::where('email', $data['email'])->exists()) {
            return false;
        }

        return UserRequest::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 'pending',
        ]);
    }

    public function processUserRequest($id, string $action, array $roleIds = [])
    {
        $userRequest = UserRequest::findOrFail($id);;
        if(UserInvitation::where('email', $userRequest->email)->exists()  || User::where('email', $userRequest->email)->exists())
        {
            return false;
        }

        if ($action === 'accept') {
            $userRequest->update(['status' => 'accepted']);

            $token = Str::random(60);
            $userInvitation = UserInvitation::create([
                'email' => $userRequest->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            $userInvitation->roles()->attach($roleIds);
            if(!in_array(1, $roleIds))
            {
                $userInvitation->roles()->attach(1);
            } // Attach the "default user" role (ID 1)
            Mail::to($userRequest->email)->send(new RegistrationLinkMail($token));

        } else {
            $userRequest->update(['status' => 'rejected']);
            Mail::to($userRequest->email)->send(new RejectionMail());
        }
        return true;
    }

    public function completeUserRegistration(array $data)
    {
        $invitation = UserInvitation::where('email', $data['email'])
            ->where('token', $data['token'])
            ->firstOrFail();

        $roles = $invitation->roles()->pluck('roles.id')->toArray();

        $user = User::create([
            'name' => UserRequest::where('email', $data['email'])->first()->name,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if(!in_array(1, $roles))
        {
            $user->roles()->attach(1);
        } // Attach the "user" role (ID 1)
        $user->roles()->attach($roles);
        $invitation->delete();

        return $user;
    }

    public function getInvitationByToken(string $token)
    {
        return UserInvitation::where('token', $token)->first();
    }

    public function getInvitationByEmail(string $email)
    {
        return UserInvitation::where('email', $email)->first();
    }

    public function createUserInvitation(array $data)
    {
        $token = Str::random(60);

        if (UserInvitation::where('email', $data['email'])->exists()|| User::where('email', $data['email'])->exists()) {
            return false;
        }

        $userInvitation = UserInvitation::create([
            'email' => $data['email'],
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        UserRequest::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 'accepted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userInvitation->roles()->attach($data['role_ids']);
        if(!in_array(1, $data['role_ids']))
        {
            $userInvitation->roles()->attach(1);
        }// Attach the "public" role (ID 1)
        Mail::to($data['email'])->send(new RegistrationLinkMail($token));

        return $userInvitation;
    }
    public function createSuperadminInvitation(array $data)
    {
        $token = Str::random(60);

         if (UserInvitation::where('email', $data['email'])->exists()|| User::where('email', $data['email'])->exists()) {
            return false;
        }

        $userInvitation = UserInvitation::create([
            'email' => $data['email'],
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        UserRequest::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 'accepted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userInvitation->roles()->attach(2);
        if(!in_array(1, $data['role_ids']))
        {
            $userInvitation->roles()->attach(1);
        } // Attach the "user" role (ID 1)
        Mail::to($data['email'])->send(new RegistrationLinkMail($token));
        return $userInvitation;
    }

    public function getPendingsUsersRequests(){
        return UserRequest::where('status', 'pending')->get();
    }

    public function getAcceptedUsersRequests(){
        $userRequests = UserRequest::where('status', 'accepted')->get();
        return UserInvitation::whereIn('email', $userRequests->pluck('email'))->get();
    }

    public function resendMail(string $email): bool
    {
        $invitation = UserInvitation::where('email', $email)->first();
        if (!$invitation) {
            return false;
        }

        Mail::to($email)->send(new RegistrationLinkMail($invitation->token));
        return true;
    }
}
