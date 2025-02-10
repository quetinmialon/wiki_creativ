<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CredentialService
{
    public function storeCredential(array $data)
    {
        if (!Auth::check()) {
            return ['error' => 'vous devez être connecté pour ajouter des logs'];
        }

        $data['user_id'] = Auth::id();
        $data['password'] = Crypt::encryptString($data['password']);

        Credential::create($data);

        return ['success' => 'logs créés avec succès'];
    }

    public function getUserRoles()
    {
        return Auth::check() ? Auth::user()->roles : null;
    }

    public function getUserCredentials()
    {
        if (!Auth::check()) {
            return ['error' => 'vous devez être connecté pour voir vos logs'];
        }

        $user = Auth::user();
        $credentials = Credential::where('user_id', $user->id)->get();
        $roleIds = $user->roles->pluck('id');
        $sharedCredentials = Credential::whereIn('role_id', $roleIds)->get();

        foreach ($credentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }

        foreach ($sharedCredentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }

        return [
            'personnal_credentials' => $credentials,
            'shared_credentials' => $sharedCredentials
        ];
    }

    public function deleteCredential($id)
    {
        if (!Auth::check()) {
            return ['error' => 'vous devez être connecté pour supprimer des logs'];
        }

        $credential = Credential::find($id);

        if (!$credential || $credential->user_id != Auth::id()) {
            return ['error' => 'vous ne pouvez pas supprimer ce log'];
        }

        $credential->delete();
        return ['success' => 'logs supprimés avec succès'];
    }

    public function getCredentialForEdit($id)
    {
        if (!Auth::check()) {
            return ['error' => 'vous devez être connecté pour modifier des logs'];
        }

        $credential = Credential::find($id);

        if (!$credential || $credential->user_id != Auth::id()) {
            return ['error' => 'vous ne pouvez pas modifier ce log'];
        }

        $credential->password = Crypt::decryptString($credential->password);
        return [
            'credential' => $credential,
            'roles' => Auth::user()->roles,
            'roleList' => Role::all()
        ];
    }

    public function updateCredential($id, array $data)
    {
        if (!Auth::check()) {
            return ['error' => 'vous devez être connecté pour modifier des logs'];
        }

        $data['password'] = Crypt::encryptString($data['password']);
        $credential = Credential::find($id);

        if (!$credential || $credential->user_id != Auth::id()) {
            return ['error' => 'vous ne pouvez pas modifier ce log'];
        }

        $credential->update($data);
        return ['success' => 'logs modifiés avec succès'];
    }
}
