<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->authService->login($credentials)) {
            if ($this->authService->getCurrentUser()->roles()->where('name', 'supervisor')->exists()) {
                return redirect()->intended('/supervisor');
            }
            return redirect()->intended('/');
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return redirect('/login')->with('success', 'Déconnexion réussie.');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = $this->authService->sendResetLink($request->email);

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['success' =>'Lien de réinitialisation envoyé avec succès.'])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'token' => ['required'],
        ]);

        $status = $this->authService->resetPassword($request->only('email', 'password', 'token'));

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password reset successful.')
            : back()->withErrors(['email' => 'Reset token expired or invalid.']);
    }
}
