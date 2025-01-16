<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use App\Models\Role;
use App\Models\User;
use App\Models\User\UserInvitation;
use App\Models\User\UserRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        UserRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Votre demande a été envoyée.');
    }

    public function subscribe(): View
    {
        return view('user_form_first_step');
    }

    public function process($id, Request $request)
    {
        $userRequest = UserRequest::findOrFail($id);

        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($request->action === 'accept') {
            $userRequest->update(['status' => 'accepted']);
            $token = Str::random(60);
            $UserInvitation = UserInvitation::create([
                'email' => $userRequest->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            $UserInvitation->roles()->attach($request->role_ids);

            Mail::to($userRequest->email)->send(new RegistrationLinkMail($token));
        } else {
            $userRequest->update(['status' => 'rejected']);
            Mail::to($userRequest->email)->send(new RejectionMail());
        }

        return redirect()->back()->with('success', 'La demande a été traitée.');
    }

    public function completeRegistration(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user_invitations,email',
            'password' => 'required|min:8',
        ]);
        $invitation = UserInvitation::where('email', $request->email)
            ->where('token', $request->token)
            ->firstOrFail();
        $roles = $invitation->roles()->pluck('roles.id')->toArray();
        $user = User::create([
            'name' => UserRequest::where('email', $request->email)->first()->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->roles()->attach($roles);

        $invitation->delete();

        return redirect()->route('login')->with('success', 'Inscription complétée.');
    }

    public function choosePassword($token){
        $invitation = UserInvitation::where('token',$token)->first();

        if(!$invitation){
            abort(404);
        }

        return view('user_form_password_step', ['email' => $invitation->email, 'token' => $token]);
    }
    public function createUserInvitationForm(){
        $roles = Role::all();
        return view('admin_create_user_invitation',['roles'=>$roles]);
    }

    public function createUserInvitation(Request $request){
        $request->validate([
            'name' =>'required|string',
            'email' =>'required|email|unique:users,email',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);
        $userInvitation = UserInvitation::create([
            'email'=>$request->email,
            'token'=> Str::random(60),
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
        UserRequest::create([
            'name' => $request->name,
            'email' => $request->email,
           'status' => 'accepted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userInvitation->roles()->attach($request->role_ids);

        Mail::to($request->email)->send(new RegistrationLinkMail(DB::table('user_invitations')->where('email', $request->email)->first()->token));

        return redirect()->route('admin')->with('success', 'Utilisateur créé avec succès.');
    }
}
