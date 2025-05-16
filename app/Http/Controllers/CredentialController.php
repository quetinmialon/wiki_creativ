<?php

namespace App\Http\Controllers;

use App\Services\CredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credential;
use Illuminate\Support\Facades\Gate;

class CredentialController extends Controller
{
    protected CredentialService $credentialService;

    public function __construct(CredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        $response = $this->credentialService->storeCredential($request->all());

        return redirect()->back()->with(key($response), reset($response));
    }

    public function create()
    {
        $roles = $this->credentialService->getUserRoles();

        if (!$roles) {
            return redirect()->back()->with('error', 'vous devez être connecté pour ajouter des logs');
        }
        return view('credentials.create-credentials', ['roles' => $roles]);
    }

    public function index()
    {
        $credentials = $this->credentialService->getUserCredentials();

        if (isset($credentials['error'])) {
            return redirect()->back()->with('error', $credentials['error']);
        }

        return view('credentials.credentials', $credentials);
    }

    public function destroy($id)
    {
        $credential = Credential::find($id);
        if ($credential->role_id == null && $credential ->user_id != Auth::id()) {
            return redirect()->back()->with('error', "vous ne pouvez pas supprimer ce log");
        }
        if( $credential->role_id != null&&!Gate::allows('manage-shared-credential',$credential) ){
            abort(403);
        }
        $response = $this->credentialService->deleteCredential($id);

        return redirect()->back()->with(key($response), reset($response));
    }

    public function edit($id)
    {
        $credential = Credential::find($id);
        if ($credential->role_id == null && $credential ->user_id != Auth::id()) {
            return redirect()->back()->with('error', "vous ne pouvez pas modifier ce log");
        }
        if( $credential->role_id != null&&!Gate::allows('manage-shared-credential',$credential) ){
            abort(403);
        }
        $response = $this->credentialService->getCredentialForEdit($id);

        if (isset($response['error'])) {
            return redirect()->back()->with('error', $response['error']);
        }

        return view('credentials.edit-credentials', $response);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        $credential = Credential::find($id);

        if ($credential->role_id == null && $credential ->user_id != Auth::id()) {
            return redirect()->back()->with('error', "vous ne pouvez pas modifier ce log");
        }

        if( $credential->role_id != null&&!Gate::allows('manage-shared-credential',$credential) ){
            abort(403);
        }
        $response = $this->credentialService->updateCredential($id, $request->all());

        return redirect()->back()->with(key($response), reset($response));
    }
}
