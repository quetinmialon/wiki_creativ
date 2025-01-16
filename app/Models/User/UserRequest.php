<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    protected $fillable = [
        'id','name','email','status'
    ];

    
}
