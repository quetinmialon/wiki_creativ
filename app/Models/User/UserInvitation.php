<?php

namespace App\Models\User;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $token
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInvitation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserInvitation extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'email', 'token',
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'user_invitation_role');
    }
}
