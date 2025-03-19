<?php

namespace App\Providers;

use App\Models\Document;
use App\Policies\DocumentPolicy;
use App\Services\AuthService;
use App\Services\CredentialService;
use App\Services\DocumentService;
use App\Services\FavoriteService;
use App\Services\LogService;
use App\Services\RoleService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Document::class, DocumentPolicy::class);
    }
}
