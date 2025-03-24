<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Category;
use App\Models\Credential;
use App\Models\Document;
use App\Models\Role;
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
            return $user->roles->contains('name',"$role admin") || $user->roles->contains('name','superadmin');
        });

        // Gérer les autorisations pour les catégories
        Gate::define('manage-category', function (User $user, Category $category) {
            return $this->isAdmin($user, $category->role);
        });

        // Gérer les autorisations pour les documents
        Gate::define('manage-document', function (User $user, Document $document) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $categoryRoles = $document->categories->pluck('role.name')->toArray();
            if ($user->id === $document->created_by) {
                return true;
            }
            foreach ($categoryRoles as $categoryRole) {
                $adminRole = 'Admin '.$categoryRole;

                if (in_array($adminRole, $userRoles)) {
                    return true;
                }
            }
            return false;
        });
        Gate::define('view-document',function(User $user, Document $document){
            $userRoles = $user->roles->pluck('name')->toArray();
            $categoriesRoles = $document->categories->pluck('role.name')->toArray();
            return in_array('superadmin', $userRoles) || count(array_intersect($categoriesRoles, $userRoles)) > 0;
        });
        Gate::define('manage-shared-credential', function (User $user, Credential $credential) {
            return $this->isAdmin($user, $credential->role); ;
        });
    }
}
