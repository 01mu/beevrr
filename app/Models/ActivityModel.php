<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class ActivityModel extends Model
{
    protected $table = 'activities';
    public $timestamps = false;
    protected $dateFormat = 'U';

    /* insert new activity when a user does something (vote, respond, or post
     * a discussion)
     *
     * args:    $type = type of activity (see "activities" migration)
     *          $prop = proposition id
     *          $time = time of input
     * returns: none
     */
    public static function insert($type, $prop, $time)
    {
        $activity_insert = new ActivityModel;

        $activity_insert->user_id = Auth::user()->id;
        $activity_insert->user_name = Auth::user()->user_name;
        $activity_insert->action_type = $type;
        $activity_insert->proposition = $prop;
        $activity_insert->date = $time;
        $activity_insert->save();
    }

    public static function get_activities($user_id, $act, $bet, $off, $pag)
    {
        switch(count($bet))
        {
            case 2:
                if($act)
                {
                    return ActivityModel::where('user_id', $user_id)
                        ->whereBetween('action_type', [$bet[0], $bet[1]])
                        ->where('is_active', 1)
                        ->orderBy('date', 'DESC')
                        ->skip($off)
                        ->take($pag)
                        ->get();
                }

                return ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [$bet[0], $bet[1]])
                    ->orderBy('date', 'DESC')
                    ->skip($off)
                    ->take($pag)
                    ->get();
            case 1:
                if($act)
                {
                    return ActivityModel::where('user_id', $user_id)
                        ->where('action_type', $bet[0])
                        ->where('is_active', 1)
                        ->orderBy('date', 'DESC')
                        ->skip($off)
                        ->take($pag)
                        ->get();
                }

                return ActivityModel::where('user_id', $user_id)
                    ->where('action_type', $bet[0])
                    ->orderBy('date', 'DESC')
                    ->skip($off)
                    ->take($pag)
                    ->get();
            default:
                return ActivityModel::where('user_id', $user_id)
                    ->orderBy('date', 'DESC')
                    ->skip($off)
                    ->take($pag)
                    ->get();
        }
    }
}
