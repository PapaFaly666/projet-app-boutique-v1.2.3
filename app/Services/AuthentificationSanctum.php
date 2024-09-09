<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthentificationSanctum implements AuthenticationServiceInterface
{
    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user()->createToken('authToken')->plainTextToken;
        }

        return null;
    }

    public function logout()
    {
        Auth::user()->tokens()->delete(); // Révoque tous les tokens Sanctum
        Auth::logout();
    }
}
