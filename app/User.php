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
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
