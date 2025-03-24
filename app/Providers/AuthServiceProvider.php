<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Credential;
use App\Models\Document;
use App\Policies\AdminPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    private function isAdmin(User $user, string $role): bool
    {
        return $user->role === "$role admin";
    }
    public function boot(): void
    {
        Gate::define('has-role', function (User $user, string $role) {
            return $user->roles->contains('name', $role) || $user->roles->contains('name', "$role admin");
        });

        Gate::define('is-superadmin', function (User $user) {
            return $user->roles->contains('name', 'superadmin');
        });


        // Vérifier si l'utilisateur est admin de son rôle
        Gate::define('is-admin', function (User $user, string $role) {
            return $user->role === "$role admin";
        });

        // Gérer les autorisations pour les catégories
        Gate::define('manage-category', function (User $user, Category $category) {
            return $this->isAdmin($user, $category->role);
        });

        // Gérer les autorisations pour les documents
        Gate::define('manage-document', function (User $user, Document $document) {
            return $user->id === $document->user_id || $this->isAdmin($user, $document->category->role);
        });

        Gate::define('manage-shared-credential', function (User $user, Credential $credential) {
            return $this->isAdmin($user, $credential->role); ;
        });
    }
}
