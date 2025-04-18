<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\UserService;
use Gate;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    protected $userService;
    protected $subscriptionService;
    public function __construct(UserService $userService, SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->userService = $userService;
    }
    protected function getSuperadminRole()
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        return Role::where('name', 'superadmin')->first();
    }
    public function index()
    {
        $user = $this->userService->getAllUsersWithRoles();
        return view("supervisor.index",compact('user'));
    }
    public function promoteUserIntoSuperAdmin($userId)
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $this->userService->addingRoleToUser(
            $userId,
            $this->getSuperadminRole()->id
        );
        return redirect()->route('supervisor.index')->with('success', 'Utilisateur promu au rôle de superadmin avec succès.');
    }
    public function revokeRoleSuperAdminOnUser($userId)
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $this->userService->removeRoleFromUser(
            $userId,
            $this->getSuperadminRole()->id
        );
        return redirect()->route('supervisor.index')->with('success', 'Rôle de superadmin retiré avec succès.');

    }
    public function revokeUser($userId)
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $this->userService->revokeUser($userId);
        return redirect()->route('supervisor.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function revokedUsersList()
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $users = $this->userService->getRevokedUsers();
        return view("supervisor.inactive_users_list",compact('users'));
    }
    public function restoreUser($userId)
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $this->userService->restoreUser($userId);
        return redirect()->route('supervisor.revokedUsers')->with('success', 'Utilisateur restauré avec succès.');
    }
    public function sendSuperadminInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $this->subscriptionService->createSuperadminInvitation($request->all());
        return redirect()->route('supervisor.index')->with('success', 'Invitation envoyée avec succès.');
    }
    public function changePasswordForm()
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        return view('supervisor.change_password');
    }
    public function changePassword(Request $request)
    {
        if(!Gate::allows('supervisor', Auth::user())){
            return abort(404);
        }
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);
        $this->userService->updateUser(
            Auth::user(),
            ['password' => bcrypt($request->password)]
        );
        return redirect()->route('supervisor.index')->with('success', 'Mot de passe changé avec succès.');
    }
}
