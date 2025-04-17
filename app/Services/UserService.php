<?php

namespace App\Services;

use App\Models\User;


class UserService{
    public function getAllUsersWithRoles()
    {
        return User::with('roles')->get();
    }

    public function getUserById($id)
    {
        return User::find($id)->with('roles')->first();
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

    public function getUserWithRoles($user_id)
    {
        return User::with('roles')->find($user_id);
    }

    public function revokeUser($user_id)
    {
        User::find($user_id)->delete();
    }

    public function searchUsers($query)
    {
        return User::where('name', 'LIKE', "%{$query}%")
                       ->orWhere('email', 'LIKE', "%{$query}%")
                       ->get();
    }
}
