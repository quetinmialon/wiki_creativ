<?php

namespace App\Models;

use App\Models\User\UserInvitation;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    public function user_invitation()
    {
        return $this->hasMany(UserInvitation::class,'user_id');
    }
}
