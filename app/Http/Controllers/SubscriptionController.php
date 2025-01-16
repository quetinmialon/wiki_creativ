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
    /**
     * create a new reegister Request
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        UserRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'pending', //default state when user try to register
        ]);

        return redirect()->back()->with('success', 'Votre demande a été envoyée.');
    }

    /**
     * display the register form first step (user choose his name and mail)
     */

    public function subscribe(): View
    {
        return view('user_form_first_step');
    }

    /**
     * Admin choose to accept or denie register, set some role to the future user and send a mail to give the answer back
     * @param mixed $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process($id, Request $request)
    {
        //check if the request exists in database and get an instance of the entity
        $userRequest = UserRequest::findOrFail($id);


        if ($request->action === 'accept') {
            $request->validate([
                'role_ids' => 'array',
                'role_ids.*' => 'exists:roles,id',
            ]);
            $userRequest->update(['status' => 'accepted']);
            // create a token that will be send by mail and use to recognize the request
            $token = Str::random(60);
            $UserInvitation = UserInvitation::create([
                'email' => $userRequest->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            // attach roles to invitation so it can be passed to future user

            $UserInvitation->roles()->attach($request->role_ids);

            // send mail with the token in the link

            Mail::to($userRequest->email)->send(new RegistrationLinkMail($token));

        } else {

            // send a rejection mail if the request has been rejected
            $userRequest->update(['status' => 'rejected']);
            Mail::to($userRequest->email)->send(new RejectionMail());
        }
        //redirect to admin index page
        return redirect()->back()->with('success', 'La demande a été traitée.');
    }


/**
 * @param Request $request
 * use the token received by mail and the chosen password to transform an invitation into a user keeping their roles
 */

    public function completeRegistration(Request $request)
    {
        //validate data to create the future user
        $request->validate([
            'email' => 'required|email|exists:user_invitations,email',
            'password' => 'required|min:8',
        ]);
        //check if invitation exists thanks to the token and the mail associated
        $invitation = UserInvitation::where('email', $request->email)
            ->where('token', $request->token)
            ->firstOrFail();
        //get the roles associated with the invitation and create a new user with the name and email of the request and the chosen password
        $roles = $invitation->roles()->pluck('roles.id')->toArray();
        $user = User::create([
            'name' => UserRequest::where('email', $request->email)->first()->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        //associate the invitation_role to the user_role
        $user->roles()->attach($roles);

        //delete the invitation after the user has been created
        $invitation->delete();

        //redirect on the login screen
        return redirect()->route('login')->with('success', 'Inscription complétée.');
    }

    /**
     * use the token received by mail to create a form to choose the password for the user according to their mail
     * @param mixed $token
     * @return View
     */
    public function choosePassword($token){

        //check if the token exists in database to fullfill email and name field or abort the creation
        $invitation = UserInvitation::where('token',$token)->first();

        if(!$invitation){
            abort(404);
        }

        return view('user_form_password_step', ['email' => $invitation->email, 'token' => $token]);
    }

    /**
     * create a form that admin can use to created a new invitation to a user with mail, name and roles
     * @return View
     */
    public function createUserInvitationForm(){
        $roles = Role::all();
        return view('admin_create_user_invitation',['roles'=>$roles]);
    }

    /**
     * with the treatment of the previous form submission create a new invitation with roles,
     * mail and name and send an email to the future user so he can choose a password
     */
    public function createUserInvitation(Request $request){
        // validate the request data before creating a new invitation and user request
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
        //attach roles to the user_invitation
        $userInvitation->roles()->attach($request->role_ids);

        //and send a creation mail
        Mail::to($request->email)->send(new RegistrationLinkMail(DB::table('user_invitations')->where('email', $request->email)->first()->token));

        return redirect()->route('admin')->with('success', 'Utilisateur créé avec succès.');
    }
}
