<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\User;
use App\Observers\ClientObserver;
use App\Observers\UserObserver;
use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryImp;
use App\Repository\ClientRepository;
use App\Repository\ClientRepositoryImp;
use App\Repository\DetteRepository;
use App\Repository\DetteRepositoryImp;
use App\Services\ArticleService;
use App\Services\ArticleServiceImp;
use App\Services\ClientServiceImpl;
use App\Services\DetteService;
use App\Services\DetteServiceImp;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('client_repository',function($app){
            return new ClientRepositoryImp();
        });

        $this->app->singleton('client_service',function($app){
            return new ClientServiceImpl();
        });

        $this->app->singleton(ArticleRepository::class, ArticleRepositoryImp::class);

        $this->app->singleton(ArticleService::class, function($app) {
            return new ArticleServiceImp($app->make(ClientRepository::class));
        });

        $this->app->singleton(DetteRepository::class, DetteRepositoryImp::class);

        $this->app->singleton(DetteRepository::class, DetteRepositoryImp::class);

        $this->app->singleton(DetteService::class,function($app){
            return new DetteServiceImp($app->make(DetteRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Client::observe(ClientObserver::class);

    }
}
