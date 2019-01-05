<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use beevrr\Models\ActivityModel;
use beevrr\Http\Controllers\Common;

use Auth;

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
     *          $pa = pre-argument phase end date
     *          $a = argument phase end date
     *          $v = post-argument phase end date
     *          $time = current time
     * returns: none
     */
    public static function insert($prop, $arg, $pa, $a, $v, $time)
    {
        $discussion_insert = new DiscussionModel;

        $discussion_insert->proposition = $prop;
        $discussion_insert->argument = $arg;
        $discussion_insert->user_name = Auth::user()->user_name;
        $discussion_insert->user_id = Auth::user()->id;
        $discussion_insert->pa_end_date = $pa;
        $discussion_insert->a_end_date = $a;
        $discussion_insert->v_end_date = $v;
        $discussion_insert->post_date = $time;
        $discussion_insert->recent_action = $time;
        $discussion_insert->save();

        $prop_id = $discussion_insert->id;

        ActivityModel::insert(7, $prop_id, $time);
    }

    /* update discussion
     *
     * args:    $disc_id = discussion id
     *          $type = type to be updated (vote or response count)
     *          $time = curren time
     * returns: none
     */
    public static function update_disc($disc_id, $type, $time)
    {
        $discussion_update = DiscussionModel::find($disc_id);

        switch($type)
        {
            case 'vote':
                $discussion_update->vote_count += 1;
                break;
            default:
                $discussion_update->reply_count += 1;
                break;
        }

        $discussion_update->recent_action = $time;
        $discussion_update->save();
    }

    /* get index page with discussions
     *
     * args:    $page = limit
     *          $pagination  = pagination
     * returns: none
     */
    public static function get_index($page, $pagination)
    {
        $discussions = DiscussionModel::orderBy('recent_action', 'DESC')
            ->skip(Common::get_offset($page))
            ->take($pagination)
            ->get();

        Common::fix_time($discussions);

        return $discussions;
    }

    /* return search results and fix time
     *
     * args:    $query = search query
     *          $page = limit
     *          $pagination = pagination
     * returns: none
     */
    public static function search_results($query, $page, $pagination)
    {
        $search_results = DiscussionModel::orderBy('recent_action', 'DESC')
            ->skip(Common::get_offset($page))
            ->take($pagination)
            ->where('proposition', 'like', $query)
            ->orWhere('proposition', 'like', $query)
            ->get();

        Common::fix_time($search_results);

        return $search_results;
    }

    /* select discussion based on id and fix its time
     *
     * args:    $disc_id = discussion id
     * returns: none
     */
    public static function select_from($disc_id)
    {
        $discussion = DiscussionModel::select('*')
            ->where('id', $disc_id)
            ->get()
            ->first();

        if($discussion)
        {
            $discussion->post_date = Common::tm($discussion->post_date);
        }

        return $discussion;
    }

    /* check if the logged in user posted the discussion being viewed
     *
     * args:    $disc_id = discussion id
     *          $user_id = user id
     * returns: none
     */
    public static function same_poster($disc_id, $user_id)
    {
        return DiscussionModel::select('id')
            ->where('id', $disc_id)
            ->where('user_id', $user_id)
            ->get();
    }
}
