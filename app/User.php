<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Passport\HasApiTokens;

use Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
    public static function update_stat($type)
    {
        $user_update = User::find(Auth::user()->id);

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

    public static function update_profile($type, $new)
    {
        $user_update = User::find(Auth::user()->id);

        switch($type)
        {
            case 'pass':
                $user_update->password = $new;
                break;
            default:
                $user_update->bio = $new;
                break;
        }

        $user_update->save();
    }

    public static function select_from($user_id)
    {
        return User::select('*')->where('id', $user_id)->get();
    }
}
