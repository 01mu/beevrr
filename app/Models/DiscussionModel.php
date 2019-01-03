<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;
use beevrr\Models\ActivityModel;

class DiscussionModel extends Model
{
    protected $table = 'discussions';
    public $timestamps = false;
    protected $dateFormat = 'U';

    /* insert new discussion
     *
     * args:    $prop = proposition text
     *          $arg = argument text
     *          $user_name = user name
     *          $user_id = user id
     *          $pa_end_date = pre-argument phase end date
     *          $a_end_date = argument phase end date
     *          $v_end_date = post-argument phase end date
     *          $time = current time
     * returns: none
     */
    public static function insert($prop, $arg, $user_name, $user_id,
        $pa_end_date, $a_end_date, $v_end_date, $time)
    {
        $discussion_insert = new DiscussionModel;

        $discussion_insert->proposition = $prop;
        $discussion_insert->argument = $arg;
        $discussion_insert->user_name = $user_name;
        $discussion_insert->user_id = $user_id;
        $discussion_insert->pa_end_date = $pa_end_date;
        $discussion_insert->a_end_date = $a_end_date;
        $discussion_insert->v_end_date = $v_end_date;
        $discussion_insert->post_date = $time;
        $discussion_insert->recent_action = $time;
        $discussion_insert->save();

        $prop_id = $discussion_insert->id;

        ActivityModel::insert($user_id, $user_name, 7, $prop_id, $time);
    }
}
