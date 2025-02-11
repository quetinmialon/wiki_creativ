<?php

namespace App\Http\Controllers;

use App\Services\CredentialService;
use Illuminate\Http\Request;

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

        return view('create-credentials', ['roles' => $roles]);
    }

    public function index()
    {
        $credentials = $this->credentialService->getUserCredentials();

        if (isset($credentials['error'])) {
            return redirect()->back()->with('error', $credentials['error']);
        }

        return view('credentials', $credentials);
    }

    public function destroy($id)
    {
        $response = $this->credentialService->deleteCredential($id);

        return redirect()->back()->with(key($response), reset($response));
    }

    public function edit($id)
    {
        $response = $this->credentialService->getCredentialForEdit($id);

        if (isset($response['error'])) {
            return redirect()->back()->with('error', $response['error']);
        }

        return view('edit-credentials', $response);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'role_id' => 'exists:roles,id'
        ]);

        $response = $this->credentialService->updateCredential($id, $request->all());

        return redirect()->back()->with(key($response), reset($response));
    }
}
