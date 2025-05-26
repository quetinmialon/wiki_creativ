<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return true;
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
    }

    public function sendResetLink(string $email)
    {
        return Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $credentials)
    {
        return Password::reset($credentials, function ($user, $password): void {
            $user->password = Hash::make($password);
            $user->save();
        });
    }
    public function getCurrentUser()
    {
        return Auth::user();
    }
}
