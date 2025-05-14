<?php

namespace App\Services;

use App\Models\Credential;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CredentialService
{
    public function storeCredential(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['password'] = Crypt::encryptString($data['password']);

        Credential::create($data);

        return ['success' => 'logs créés avec succès'];
    }

    public function getUserRoles()
    {
        return Auth::user();
    }

    public function getUserCredentials()
    {
        $user = Auth::user();
        $credentials = Credential::where('user_id', $user->id)->get();
        $roleIds = $user->roles->pluck('id');

        // Récupérer les credentials partagés avec les rôles
        $sharedCredentials = Credential::whereIn('role_id', $roleIds)
            ->with('role')
            ->get();

        // Déchiffrement des mots de passe
        foreach ($credentials as $credential) {
            $credential->password = Crypt::decryptString($credential->password);
        }

        // Organiser les credentials partagés par rôle
        $groupedSharedCredentials = [];
        foreach ($sharedCredentials as $credential) {
            if ($credential->role) {
                $roleName = $credential->role->name;
                $credential->password = Crypt::decryptString($credential->password);
                $groupedSharedCredentials[$roleName][] = $credential;
            }
        }

        return [
            'personnal_credentials' => $credentials,
            'shared_credentials' => $groupedSharedCredentials
        ];
    }


    public function deleteCredential($id)
    {
        $credential = Credential::find($id);

        $credential->delete();
        return ['success' => 'logs supprimés avec succès'];
    }

    public function getCredentialForEdit($id)
    {
        $credential = Credential::find($id);

        $credential->password = Crypt::decryptString($credential->password);
        return [
            'credential' => $credential,
            'roleList' => Auth::user()->roles,
        ];
    }

    public function updateCredential($id, array $data)
    {
        $data['password'] = Crypt::encryptString($data['password']);
        $credential = Credential::find($id);

        $credential->update($data);
        return ['success' => 'logs modifiés avec succès'];
    }
}
