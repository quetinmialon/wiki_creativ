<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        "name",
        'role_id',
    ];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'category_document', 'category_id', 'document_id');
    }

}
