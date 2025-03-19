<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Policies\AdminPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->roles()->whereIn('name', ['admin', 'superadmin'])->exists();
        });



    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
