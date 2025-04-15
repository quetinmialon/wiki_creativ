<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserRequest extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'id','name','email','status'
    ];
}
