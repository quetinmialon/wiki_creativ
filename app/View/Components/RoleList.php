<?php

namespace App\View\Components;

use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RoleList extends Component
{
    /**
     * Create a new component instance.
     */
    public $roles;

    public function __construct()
    {
        $this->roles = Role::all();
    }

    public function render(): View
    {
        return view(view: 'components.role-list', data: [$this->roles]);
    }
}
