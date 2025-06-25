<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $response = $this->subscriptionService->createUserRequest($request->only(['name', 'email']));

        if ($response  === false) {
            return redirect()->back()->withErrors(['email' => 'Une demande avec cet email existe déjà ou un utilisateur est déjà enregistré avec cet email.']);
        }

        return redirect()->route('login')->with('success', 'Votre demande a été envoyée.');
    }

    public function subscribe(): View
    {
        return view('register.user_form_first_step');
    }

    public function process($id, Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|string|in:accept,reject',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $response = $this->subscriptionService->processUserRequest($id, $request->action, $request->role_ids ?? []);
        if ($response === false) {
            return redirect()->back()->withErrors(['email' => 'Une invitation avec cet email existe déjà.']);
        }

        return redirect()->back()->with('success', 'La demande a été traitée.');
    }

    public function completeRegistration(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:user_invitations,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'token' => 'required|string',
        ]);

        $this->subscriptionService->completeUserRegistration($request->only(['email', 'password', 'token']));

        return redirect()->route('login')->with('success', 'Inscription complétée.');
    }

    public function choosePassword($token): View
    {
        $invitation = $this->subscriptionService->getInvitationByToken($token);

        if (!$invitation) {
            abort(404);
        }

        return view('register.user_form_password_step', ['email' => $invitation->email, 'token' => $token]);
    }

    public function createUserInvitationForm(): View
    {
        $roles = Role::all();
        return view('admin.admin_create_user_invitation', ['roles' => $roles]);
    }

    public function createUserInvitation(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $response = $this->subscriptionService->createUserInvitation($request->only(['name', 'email', 'role_ids']));
        if ($response === false) {
            return redirect()->back()->withErrors(['email' => 'Une invitation avec cet email existe déjà.']);
        }
        return redirect()->route('admin')->with('success', 'Utilisateur créé avec succès.');
    }

    public function getAcceptedRequests(): View
    {
        $requests = $this->subscriptionService->getAcceptedUsersRequests();
        return view('admin.admin_accepted_requests', ['userRequests' => $requests]);
    }
    public function resendMail($email): RedirectResponse
    {
        $response = $this->subscriptionService->resendMail($email);
        if ($response === false) {
            return redirect()->back()->withErrors('Une erreur est survenue lors de l\'envoi de l\'email. Veuillez réessayer plus tard.');
        }
        return redirect()->back()->with('success', 'Email de réinvitation envoyé avec succès.');
    }
}
