<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }
    public function permissions()
    {
        return $this->hasMany(Permission::class,'author');
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function credentials(){
        return $this->hasMany(Credential::class);
    }

    public function logs(){
        return $this->hasMany(Log::class);
    }

    public function wrote(){
        return $this->hasMany(Document::class, 'created_by');
    }

    public function updated_documents(){
        return $this->hasMany(Document::class, 'updated_by');
    }
}
