<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class SuperAdminPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function SuperAdmin(User $user)
    {
        return Gate::allows('is-superadmin');
    }
}
