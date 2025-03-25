<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Models\Role;

class UsersByRole extends Component
{
    public $labels;
    public $data;

    public function __construct()
    {
        $roles = Role::withCount('users')->get();

        $this->labels = $roles->map(fn($role) => $role->name)->toArray();
        $this->data = $roles->map(fn($role) => $role->users_count)->toArray();
    }

    public function render()
    {
        return view('components.admin.users-by-role', [
            'labels' => $this->labels,
            'data' => $this->data,
        ]);
    }
}


