<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CredentialController extends Controller
{
    /**
     * store a new credential, can be related to a role and is related to this author
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //validate data
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
        ]);
        //check if auth
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour ajouter des logs');
        };

        $userId = Auth::id(); //get current user id
        Credential::create([
            'destination' => $request->destination,
            'username' => $request->username,
            'password' => Crypt::encryptString($request->password), //password is crypted before storage
            'user_id' => $userId,
            'role_id' => $request->role_id ?? $request->role_id, //null collessing to set role_id only if necessary
        ]);
        return redirect()->back()->with('success','logs créés avec succès');
    }
    /**
     * render create  credential form view
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {   //check auth
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour ajouter des logs');
        };
        //pluck roles to user and send it back to view
        $roles = Auth::user()->roles;
        return view('create-credentials', ['roles'=> $roles]);
    }
    /**
     * render list of credentials available for the current user, personnal and shared by roles
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        //check if auth
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour voir vos logs');
        };
        //get the current user
        $user = Auth::user();
        //get personnal credentials and shared credentials by roles from the current user
        $credentials = Credential::where('user_id', Auth::id())->get();
        $roleIds = $user->roles->pluck('id');
        $shared_credentials = Credential::whereIn('role_id', $roleIds)->get();
        //decrypt password for security reasons
        foreach ($credentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }

        foreach ($shared_credentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }
        //send back to view
        return view('credentials', ['personnal_credentials'=> $credentials, 'shared_credentials'=> $shared_credentials]);
    }
    /**
     * destroy a credential from the current user, cannot be destroy if the user isn't the author or and admin
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        //check if auth
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour supprimer des logs');
        };
        //find the credential
        $credential = Credential::find($id);
        //check if the user is the author of the credential or is an admin or superadmin.
        if($credential->user_id!= Auth::id())//todo : also add the possibility to delete when user is an admin or superadmin
        {
            return redirect()->back()->with('error','vous ne pouvez pas supprimer ce log');
        }
        //delete the credential
        $credential->delete();
        //redirect to back with success message
        return redirect()->back()->with('success','logs supprimés avec succès');
    }
    /**
     * render edit form if the user is auth and is the author of the credential
     * @param mixed $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        //check if auth and user is the author of the credential
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour modifier des logs');
        };
        //find the credential and check if the user is the author of the credential or is an admin or superadmin.
        $credential = Credential::find($id);
        if($credential->user_id!= Auth::id())//todo : also add the possibility to edit when user is an admin or superadmin
        {
            return redirect()->back()->with('error','vous ne pouvez pas modifier ce log');
        }
        //pluck roles to user and send it back to view to populate the dropdown list in the edit form.
        $roles = Auth::user()->roles;
        $credential->password = Crypt::decryptString($credential->password); //password is decrypted before showing it in the form to edit it.
        $roleList = Role::all();
        return view('edit-credentials', ['credential'=> $credential, 'roles'=> $roles, 'roleList'=> $roleList]);
    }
    /**
     * update the actual credential if the user is auth and is the author
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        //check if auth
        if(!Auth::check()){
            return redirect()->back()->with('error','vous devez etre connecté pour modifier des logs');
        };
        //validate the request
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'role_id' => 'exists:roles,id'
        ]);
        //encrypt password
        $request['password'] = Crypt::encryptString($request->password);

        //find the credential and check if the user is the author of the credential or is an admin or superadmin.
        $credential = Credential::find($id);
        if($credential->user_id!= Auth::id())//todo : also add the possibility to update when user is an admin or superadmin
        {
            return redirect()->back()->with('error','vous ne pouvez pas modifier ce log');
        };
        //update credential and redirect with success message
        $credential->update($request->all());

        return redirect()->back()->with('success','logs modifiés avec succès');
    }
}
