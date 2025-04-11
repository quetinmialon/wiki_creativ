<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property \App\Models\User|null $author
 * @property int $document_id
 * @property string|null $comment
 * @property string $status
 * @property int|null $handled_by
 * @property string|null $handled_at
 * @property string $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Document $document
 * @property-read \App\Models\User|null $handledBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereHandledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereHandledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'name',
        'status',
        'comment',
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
