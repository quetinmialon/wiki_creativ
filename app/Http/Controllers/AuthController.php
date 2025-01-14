<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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



        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email'); // Optionnel si vous souhaitez pré-remplir l'email

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }


    public function reset(Request $request){
        $request->validate(['email'=> ['required','email'],'password'=> ['required','min:8']]);
        $credentials = $request->only('email', 'password', 'token');
        $status = Password::reset($credentials, function ($user, $password) {
            $user->password =  Hash::make($password);
            $user->save();
        });
        return $status == Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', 'Password reset successful.')
        : back()->withErrors(['email' => 'Reset token expired or invalid.']);
    }
}


