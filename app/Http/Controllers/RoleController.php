<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view(view: 'role.role-list', data: ['roles' => $roles]);
    }

    public function create():View
    {
        return view(view: 'create-role-form');
    }

    public function insert(Request $request): RedirectResponse
    {
        $role = Role::create(attributes: $request->all());
        return redirect(to: '/');
    }

    public function edit(Request $request): View
    {
        $role = Role::findOrFail($request->id);
        return view(view: 'update-role-form', data: ['role' => $role]);
    }

    public function update(Request $request): RedirectResponse
    {
        $role = Role::findOrFail($request->id);
        $request->validate([
            'name' => "required|string|max:255|unique:roles,name,{$role->id}",
        ]);
        $role->update(attributes: $request->all());
        return redirect(to: '/')->with(key:'success', value: 'Rôle modifié avec succès.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $role = Role::findOrFail($request->id);
        if($role->name == 'admin'|| $role->name == 'default')
        {
            return redirect(to: '/')->with(key: 'error', value: 'Vous ne pouvez pas supprimer ce rôle.');
        }
        $role->delete();
        return redirect(to: '/')->with(key: 'success', value: 'Rôle supprimé avec succès.');
    }

}
