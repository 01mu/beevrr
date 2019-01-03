<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityModel extends Model
{
    protected $table = 'activities';
    public $timestamps = false;
    protected $dateFormat = 'U';

    /* insert new activity when a user does something (vote, respond, or post
     * a discussion)
     *
     * args:    $user_id = id of user
     *          $user_name = name of user
     *          $type = type of activity (see "activities" migration)
     *          $prop = proposition id
     *          $time = time of input
     * returns: none
     */
    public static function insert($user_id, $user_name, $type, $prop, $time)
    {
        $activity_insert = new ActivityModel;

        $activity_insert->user_id = $user_id;
        $activity_insert->user_name = $user_name;
        $activity_insert->action_type = $type;
        $activity_insert->proposition = $prop;
        $activity_insert->date = $time;
        $activity_insert->save();
    }
}
