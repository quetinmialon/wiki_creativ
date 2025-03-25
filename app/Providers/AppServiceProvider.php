<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\CredentialService;
use App\Services\DocumentService;
use App\Services\FavoriteService;
use App\Services\LogService;
use App\Services\RoleService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\SuperAdminPolicy;
use App\Policies\TemporaryAccessDocument;
use App\Services\PermissionService;
use App\Services\SubscriptionService;

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
        $this->app->singleton(DocumentService::class, function ($app) {
            return new DocumentService();
        });
        $this->app->singleton(FavoriteService::class, function ($app) {
            return new FavoriteService();
        });
        $this->app->singleton(LogService::class, function ($app) {
            return new LogService();
        });
        $this->app->singleton(PermissionService::class, function ($app) {
            return new PermissionService();
        });
        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService();
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, SuperAdminPolicy::class);
    }
}
