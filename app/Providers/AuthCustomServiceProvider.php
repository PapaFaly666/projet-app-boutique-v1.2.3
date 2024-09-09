<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthenticationServiceInterface;
use App\Services\AuthentificationPassport; // ou AuthentificationSanctum
use App\Services\AuthentificationSanctum;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        //$this->app->bind(AuthenticationServiceInterface::class, AuthentificationPassport::class);
        $this->app->bind(AuthenticationServiceInterface::class, AuthentificationSanctum::class);
    }

    public function boot()
    {
        //
    }
}
