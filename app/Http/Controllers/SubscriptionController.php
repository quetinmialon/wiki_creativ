<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;
use App\Models\Role;
use App\Models\User;
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

        if ($request->action === 'accept') {
            $userRequest->update(['status' => 'accepted']);

            // Génération d'un lien d'inscription sécurisé
            $token = Str::random(60);
            DB::table('user_invitations')->insert([
                'email' => $userRequest->email,
                'token' => $token,
                'created_at' => now(),
            ]);

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


        User::create([
            'name' => UserRequest::where('email', $request->email)->first()->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        DB::table('user_invitations')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Inscription complétée.');
    }

    public function choosePassword($token){
        $invitation = DB::table('user_invitations')->where('token',$token)->first();

        if(!$invitation){
            abort(404);
        }

        return view('user_form_password_step', ['email' => $invitation->email, 'token' => $token]);

    }

    

}
