<?php

namespace App\Services;

use App\Models\User;


class UserService{
    public function getAllUsersWithRoles()
    {
        $user = User::with('roles')->get();
        foreach ($user as $key => $u) {
            if ($u->roles->contains('name', 'supervisor')) {
                $user->forget($key);
            }
        }
        return $user;
    }

    public function getUserById($id)
    {
        return User::with('roles')->find($id);
    }


    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }
    public function updateUserRole(array $data)
    {
        $user = User::find($data['user_id']);
        $user->roles()->sync($data['role_ids']);
    }

    public function addingRoleToUser($userId, $roleId)
    {
        $user = User::find($userId);
        $user->roles()->attach($roleId);
    }
    public function removeRoleFromUser($userId, $roleId)
    {
        $user = User::find($userId);
        $user->roles()->detach($roleId);
    }
    public function getUserWithRoles($userId)
    {
        $user = User::with('roles')->find($userId);
        return $user;
    }

    public function revokeUser($userId)
    {
        User::find($userId)->delete();
    }

    public function getRevokedUsers()
    {
        return User::onlyTrashed()->get();
    }
    public function restoreUser($userId)
    {
        User::withTrashed()->find($userId)->restore();
    }

    public function searchUsers($query)
    {
        return User::where('name', 'LIKE', "%{$query}%")
                       ->orWhere('email', 'LIKE', "%{$query}%")
                       ->get();
    }

}
