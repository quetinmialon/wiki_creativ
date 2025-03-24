<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $excerpt
 * @property string $content
 * @property string|null $formated_name
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Log> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\User|null $updator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFormatedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Document extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'excerpt',
        'content',
        'formated_name',
        'created_by',
        'updated_by',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_document', 'document_id', 'category_id');
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
