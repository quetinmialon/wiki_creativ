<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $destination
 * @property string $username
 * @property string $password
 * @property int $user_id
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role|null $role
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Credential whereUsername($value)
 * @mixin \Eloquent
 */
class Credential extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'destination',
        'username',
        'password',
        'user_id',
        'role_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }

}
