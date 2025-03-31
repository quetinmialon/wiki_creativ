<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Services\UserService;

class AdminController extends Controller
{
    protected UserService $userService;

    protected RoleService $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }
    public function index()
    {
        return view("admin.admin_index");
    }

    public function UserList()
    {
        $users = $this->userService->getAllUsersWithRoles();
        return view("admin.user_list", compact('users'));
    }

    public function EditUsersRole(Request $request){
        $user = $this->userService->getUserWithRoles($request->id);
        $roles = $this->roleService->getAllRoles();
        return view('admin.edit_roles_form', compact('user', 'roles'));
    }

    public function revokeUser(Request $request)
    {
        $this->userService->revokeUser($request->id);
        return redirect()->route('admin.users')->with('success', 'Utilisateur retiré avec succès.');
    }
    public function updateUserRole(Request $request)
    {
        $this->userService->updateUserRole([
            'user_id' => $request->id,
            'role_ids' => $request->roles ?? [] // Assure que c'est bien un tableau
        ]);

        return redirect()->route('admin.users')->with('success', 'Rôle utilisateur mis à jour avec succès.');
    }

    public function RoleList(){
        return view("admin.role_list");
    }

    public function PermissionList(){
        return view("admin.permission_list");
    }

    public function UserRequests(){
        return view("admin.user_request_list");
    }

    public function searchUser(Request $request)
    {
        $query = $request->input('query');
        $users = $this->userService->searchUsers($query);
        if($users->isEmpty()){
            return redirect()->route('admin.users')->with('error', 'Aucun résultat trouvé');
        }
        return view('admin.search_users_results', compact('users', 'query'));
    }


    
}
