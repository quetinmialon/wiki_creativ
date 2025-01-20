<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'status',
        'comments',
        'handled_by',
        'author',
        'expired_at',
        'handled_at',
        'document_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class,'author');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class,'handled_by');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
