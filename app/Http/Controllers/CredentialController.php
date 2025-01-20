<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CredentialController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ]);
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour ajouter des logs');
        };
        $userId = Auth::id(); //get current user id
        Credential::create([
            'destination' => $request->destination,
            'username' => $request->username,
            'password' => Crypt::encryptString($request->password), //password is crypted before storage
            'user_id' => $userId,
            'role_id' => $request->role_id ? $request->role_id : null,
        ]);
        return redirect()->back()->with('success','logs créés avec succès');
    }

    public function create()
    {
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour ajouter des logs');
        };
        $roles = Auth::user()->roles;
        return view('create-credentials', ['roles'=> $roles]);
    }

    public function index()
    {
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour voir vos logs');
        };
        $user = Auth::user();
        $credentials = Credential::where('user_id', Auth::id())->get();
        $roleIds = $user->roles->pluck('id');
        $shared_credentials = Credential::whereIn('role_id', $roleIds)->get();
        foreach ($credentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }

        foreach ($shared_credentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }
        return view('credentials', ['personnal_credentials'=> $credentials, 'shared_credentials'=> $shared_credentials]);
    }
    public function destroy($id)
    {
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour supprimer des logs');
        };
        $credential = Credential::find($id);
        if($credential->user_id!= Auth::id())//todo : also add the possibility to delete when user is an admin or superadmin
        {
            return redirect()->back()->with('error','vous ne pouvez pas supprimer ce log');
        }
        $credential->delete();
        return redirect()->back()->with('success','logs supprimés avec succès');
    }


    public function edit($id)
    {
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour modifier des logs');
        };
        $credential = Credential::find($id);
        if($credential->user_id!= Auth::id())//todo : also add the possibility to edit when user is an admin or superadmin
        {
            return redirect()->back()->with('error','vous ne pouvez pas modifier ce log');
        }
        $roles = Auth::user()->roles;
        $credential->password = Crypt::decryptString($credential->password); //password is decrypted before showing it in the form to edit it.
        $roleList = Role::all();
        return view('edit-credentials', ['credential'=> $credential, 'roles'=> $roles, 'roleList'=> $roleList]);
    }

    public function update(Request $request, $id)
    {
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour modifier des logs');
        };
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'role_id' => 'exists:roles,id'
        ]);
        $request['password'] = Crypt::encryptString($request->password);
        $credential = Credential::find($id);
        if($credential->user_id!= Auth::id())//todo : also add the possibility to update when user is an admin or superadmin
        {
            return redirect()->back()->with('error','vous ne pouvez pas modifier ce log');
        };

        $credential->update($request->all());

        return redirect()->back()->with('success','logs modifiés avec succès');
    }
}
