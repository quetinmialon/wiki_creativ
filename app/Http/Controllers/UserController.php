<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function showProfile()
    {
        $user = $this->userService->getUserWithRoles(Auth::id());
        if (!$user) {
            abort(404);
        }
        return view('user.profile', compact('user'));
    }
    public function editProfile()
    {
        $user = $this->userService->getUserWithRoles(Auth::id());
        if (!$user) {
            abort(404);
        }
        return view('user.edit-profile', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        // Logique pour mettre à jour le profil de l'utilisateur
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->update($request->only('name', 'email'));

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour avec succès.');
    }
    public function showChangePasswordForm()
    {
        // Logique pour afficher le formulaire de changement de mot de passe
        return view('user.change-password');
    }
    public function changePassword(Request $request)
    {
        // Logique pour changer le mot de passe de l'utilisateur
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Mot de passe actuel incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Mot de passe changé avec succès.');
    }
}
