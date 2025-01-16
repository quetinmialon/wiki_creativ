<?php

namespace App\Models\User;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class UserInvitation extends Model
{
    protected $fillable = [
        'email', 'token',
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'user_invitation_role');
    }
}
