<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();
        $documentCategoryRoleIds = $document->categories()->with('role')->get()->pluck('role_id')->toArray();
        return !empty(array_intersect($userRoleIds, $documentCategoryRoleIds))  || in_array($user->roles()->pluck('name')->toArray(), (['admin', 'superadmin']));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->id === $document->user_id || in_array($user->roles()->pluck('name')->toArray(), (['admin', 'superadmin']));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id || in_array($user->roles()->pluck('name')->toArray(), (['admin', 'superadmin']));
    }


    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id || in_array($user->roles()->pluck('name')->toArray(), (['admin', 'superadmin']));
    }
}
