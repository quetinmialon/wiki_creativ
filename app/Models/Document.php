<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'name',
        'excerpt',
        'content',
        'formated_name',
        'created_by',
        'updated_by',
    ];

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function author(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function updator(){
        return $this->belongsTo(User::class,'updated_by');
    }

    public function permissions(){
        return $this->hasMany(Permission::class);
    }

    public function favorites(){
        return $this->hasMany(Favorite::class);
    }

    public function logs(){
        return $this->hasMany(Log::class);
    }
}
