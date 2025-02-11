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

    public function index(): View
    {
        $roles = $this->roleService->getAllRoles();
        return view('role.role-list', ['roles' => $roles]);
    }

    public function create(): View
    {
        return view('create-role-form');
    }

    public function insert(Request $request): RedirectResponse
    {
        $this->roleService->createRole($request->all());
        return redirect('/')->with('success', 'Rôle créé avec succès.');
    }

    public function edit(Request $request): View
    {
        $role = $this->roleService->getRoleById($request->id);
        return view('update-role-form', ['role' => $role]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => "required|string|max:255|unique:roles,name,{$request->id}",
        ]);

        $this->roleService->updateRole($request->id, $request->all());
        return redirect('/')->with('success', 'Rôle modifié avec succès.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $response = $this->roleService->deleteRole($request->id);
        return redirect('/')->with(key($response), reset($response));
    }
}
