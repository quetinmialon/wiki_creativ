<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Category;
use App\Models\Credential;
use App\Models\Document;
use App\Models\Permission;
class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-document', function (User $user, Document $document) {
            $permission = Permission::where('author', $user->id)
                ->where('document_id', $document->id)
                ->where('status', 'approved')
                ->Where('expired_at', '>', now())
                ->exists();
            return $permission;
        });

        Gate::define('has-role', function (User $user, string $role) {
            return $user->roles->contains('name', $role) || $user->roles->contains('name', "$role admin");
        });

        Gate::define('is-superadmin', function (User $user) {
            return $user->roles->contains('name', 'superadmin');
        });


        // Gérer les autorisations pour les catégories
        Gate::define('manage-category', function (User $user, Category $category) {
            return true;
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
            return true ;
        });
    }
}
