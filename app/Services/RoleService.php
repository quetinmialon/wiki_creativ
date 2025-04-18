<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function getAllRoles()
    {
        return Role::all();
    }

    public function createRole(array $data)
    {
        return Role::create($data);
    }

    public function getRoleById($id)
    {
        return Role::findOrFail($id);
    }

    public function updateRole($id, array $data)
    {
        $role = Role::findOrFail($id);
        if(in_array($role->name, ['superadmin', 'default', 'qualité','Admin qualité','supervisor'])) {
            return ['error' => 'Vous ne pouvez pas modifier ce rôle.'];
        }
        $data['name'] = $data['name'] ?? $role->name;

        $role->update($data);
        return $role;
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['superadmin', 'default', 'qualité','Admin qualité','supervisor'])) {
            return ['error' => 'Vous ne pouvez pas supprimer ce rôle.'];
        }

        $role->delete();
        return ['success' => 'Rôle supprimé avec succès.'];
    }

    public function getRolesWhereCategoriesExist(){
        return Role::whereHas('categories')->where('name', 'not like', '%Admin %')->get();
    }
}
