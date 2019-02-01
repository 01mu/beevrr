<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

use Laravel\Passport\HasApiTokens;

use Auth;
use DB;

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

    /* update a user's password or bio
    *
    * args:     $type = "bio" or "pass"
                $new = bio with stripped tags or hashed password
    * returns:  none
    */
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

    /* check to see if a user name exists in lower case for psql case issue
    *
    * args:     $user_name = user name
    * returns:  user name
    */
    public static function check_lower($user_name)
    {
        $query = 'SELECT user_name FROM users WHERE LOWER(user_name) = ?';

        return DB::select($query, [$user_name]);
    }

    /* get user info based on id
    *
    * args:     $user_id = user id
    * returns:  user
    */
    public static function select_from($user_id)
    {
        return User::select('id', 'user_name', 'score', 'total_responses',
            'active_responses', 'total_votes', 'active_votes',
            'total_discussions', 'active_discussions',
            'bio')->where('id', $user_id)->get();
    }

    /* create new user based on request
    *
    * args:     $data = data from http request
    * returns:  user model create call
    */
    public static function new_user(array $data)
    {
        return User::create([
            'user_name' => $data['user_name'],
            'password' => Hash::make($data['password']),
            'score' => 0,
            'total_responses' => 0,
            'active_responses' => 0,
            'total_votes' => 0,
            'active_votes' => 0,
            'total_discussions' => 0,
            'active_discussions' => 0,
            'bio' => '',
        ]);
    }
}
