<?php

namespace App\Services;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class AuthentificationPassport implements AuthenticationServiceInterface
{
    use HasApiTokens;

    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }

        return null;
    }

    public function logout()
    {
        Auth::logout();
    }
}
