<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\CredentialService;
use App\Services\DocumentService;
use App\Services\FavoriteService;
use App\Services\LogService;
use App\Services\RoleService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Services\PermissionService;
use App\Services\SubscriptionService;
use App\Listeners\LogDocumentOpening;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Document;
use App\Models\Category;
use App\Models\Credential;
use App\Models\Permission;
use App\Policies\SuperAdminPolicy;
use App\Services\ImageService;
use App\Services\UserService;
use Illuminate\Pagination\Paginator;

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
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        /*************define Gate and policies*******************/
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
        Gate::define('manage-category', function (User $user, Category $category) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $categoryRoles = $category->role->pluck('name')->toArray();
            foreach($categoryRoles as $role){
                $adminRole = 'Admin '.$role;
                if (in_array($adminRole, $userRoles)) {
                    return true;
                }
            }
            return in_array('superadmin', $userRoles);
        });
        Gate::define('manage-document', function (User $user, Document $document) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $categoryRoles = $document->categories->pluck('role.name')->toArray();
            if ($user->id == $document->created_by) {
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
            return in_array('superadmin', $userRoles) || count(array_intersect($categoriesRoles, $userRoles)) > 0  || Auth::user()->id === $document->created_by;
        });
        Gate::define('manage-shared-credential', function (User $user, Credential $credential) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $credentialRoles = $credential->role->pluck('name')->toArray();
            foreach($credentialRoles as $role){
                $adminRole = 'Admin '.$role;
                if (in_array($adminRole, $userRoles)) {
                    return true;
                }
            }
            return in_array('superadmin', $userRoles);
        });
        /***************** define policies  ***************************/
        Gate::policy(User::class, SuperAdminPolicy::class);

        /******************** define Listeners **********************/
        Event::listen(
            LogDocumentOpening::class
        );
        /****************** defines Route files **************/
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }
}
