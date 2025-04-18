<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\AuthService;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    protected UserService $userService;

    protected RoleService $roleService;

    protected AuthService $authService;

    public function __construct(UserService $userService, RoleService $roleService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->authService = $authService;
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
        $user = $this->userService->getUserById((int)$request->route()->parameters()['id']);

        if(!$user)
        {
            return redirect()->route('admin.users')->with('error',"l'utilisateur n'exite pas en base de donnée");
        }
        if($user->roles->contains('name','superadmin'))
        {
            return redirect()->route('admin.users')->with('error',"l'utilisateur que vous souhaitez supprimer est un superadmin, vous ne pouvez pas le supprimer");
        }
        if($user->roles->contains('name','supervisor'))
        {
            return redirect()->route('admin.users')->with('error',"touche pas à ça petit con");
        }
        $this->userService->revokeUser($user->id);
        return redirect()->route('admin.users')->with('success', 'Utilisateur retiré avec succès.');
    }
    public function updateUserRole(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'roles' => 'array|nullable',
            'roles.*' => 'exists:roles,id'
        ]);

        if (in_array(13, $request->roles)) {
            return redirect()->back()->withErrors(['roles' => "le rôle entré n'existe pas ou ne peux pas etre affecté à cet utilisateur"]);
        }
        if (in_array(2, $request->roles) && (string)$this->authService->getCurrentUser()->id !== $request->id) {
            return redirect()->back()->withErrors(['roles' => "vous ne possedez pas le droit de modifier les rôles d'un superadmin"]);
        }
        if ($this->userService->getUserById($request->id)->roles->contains('name','supervisor'))
        {
            return redirect()->back()->withErrors(['roles'=>"touche pas à ça petit con"]);
        }

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
