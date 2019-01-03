<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'user_name',
        'password',
        'score',
        'total_responses',
        'active_responses',
        'total_votes',
        'active_votes',
        'total_discussions',
        'active_discussions',
        'bio',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /* update user statistic
     *
     * args:    $user_id = user id
     *          $type = type of statistic to be updated
     * returns: none
     */
    public static function update_stat($user_id, $type)
    {
        $user_update = User::find($user_id);

        switch($type)
        {
            case 'disc':
                $user_update->total_discussions += 1;
                $user_update->active_discussions += 1;
                break;
            case 'vote':
                $user_update->total_votes += 1;
                $user_update->active_votes += 1;
                break;
            default:
                $user_update->total_responses += 1;
                $user_update->active_responses += 1;
                break;
        }

        $user_update->save();
    }
}
