<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function create(): View
    {
        return view('admin.create-role-form');
    }

    public function insert(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => "required|string|max:255|unique:roles",
        ]);
        $this->roleService->createRole($request->all());

        $adminRole = $request->all();

        $adminRole['name'] = 'Admin ' . $adminRole['name'];
        $this->roleService->createRole($adminRole);
        return redirect('/admin/roles')->with('success', 'Rôle créé avec succès.');
    }

    public function edit(Request $request): View
    {
        $role = $this->roleService->getRoleById($request->id);
        return view('admin.update-role-form', ['role' => $role]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => "required|string|max:255|unique:roles,name,{$request->id}",
        ]);
        $role = $this->roleService->updateRole($request->id, $request->all());
        if ($role === null) {
            return redirect('/admin/roles')->with('error', 'Ce rôle ne peut pas être modifié.');
        }
        $adminRole = $role;
        $adminRole['name'] = 'Admin ' . $adminRole['name'];
        $adminRole['id'] = $request->id + 1;
        $this->roleService->updateRole($adminRole['id'], $adminRole->toArray());
        return redirect('/admin/roles')->with('success', 'Rôle modifié avec succès.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $response = $this->roleService->deleteRole($request->id);
        if (isset($response['error'])) {
            return redirect('/admin/roles')->with('error', $response['error']);
        }
        //also delete related admin role
        $this->roleService->deleteRole($request->id + 1);
        return redirect('/admin/roles')->with(key($response), reset($response));
    }
}
