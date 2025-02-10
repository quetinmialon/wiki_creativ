<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\CredentialService;
use App\Services\RoleService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });
        $this->app->singleton(CredentialService::class, function ($app) {
            return new CredentialService();
        });
        $this->app->singleton(RoleService::class, function ($app) {
            return new RoleService();
        });
        $this->app->singleton(RoleService::class, function ($app) {
            return new RoleService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
