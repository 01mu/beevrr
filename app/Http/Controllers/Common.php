<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use beevrr\Models\VoteModel;
use beevrr\Models\DiscussionModel;
use beevrr\Models\ResponseModel;
use beevrr\Models\ActivityModel;
use beevrr\User;

use Validator;
use DB;
use Auth;
use DateTime;

class Common extends Controller
{
    public static function mobile_or_msg($request, $success, $msg)
    {
        if($request['mobile'])
        {
            $out = 'failure';

            if($success)
            {
                $out = 'success';
            }

            $content['status'] = $out;

            return response()->json($content, 200);
        }
        else
        {
            return Common::notice_msg($msg);
        }
    }

    /* get pagination array for route redirects and check whether user is on
     * page 0
     *
     * args:    $l = left redirect (back)
     *          $r = right redirect (forward)
     *          $page = current page
     *
     * returns: array of redirect urls and whether the user is on page 0
     */
    public static function get_pagination_next($l, $r, $page)
    {
        $content = array();

        $content['nl'] = 0;

        if($page == 0)
        {
            $content['nl'] = 1;
        }

        $content['left'] = $l;
        $content['right'] = $r;

        return $content;
    }

    /* get whether to redirect if next pagaination page is empty
     *
     * args:    $db = array of results for page
     *          $page = current page
     * returns: whether or not to redirect
     */
    public static function pagination_redirect($db, $page)
    {
        if(count($db) === 0 && ($page !== 1 && $page !== 0))
        {
            return 1;
        }

        return 0;
    }

    /* get pagination offset
     *
     * args:    $page = current page
     * returns: offset
     */
    public static function get_offset($page)
    {
        $offset = 0;

        if($page)
        {
            $offset = config('global.pagination') * $page;
        }

        return $offset;
    }

    /* set unix time (date) to time since string for array of db results
     *
     * args:    $db = the array of results
     *          $t = toggle
     * returns: none
     */
    public static function fix_time($db, $t = 0)
    {
        if(!$t)
        {
            for($i = 0; $i < count($db); $i++)
            {
                $db[$i]->post_date = Common::tm($db[$i]->post_date);
            }
        }
        else
        {
            for($i = 0; $i < count($db); $i++)
            {
                $db[$i]->date = Common::tm($db[$i]->date);
            }
        }
    }

    /* if the discussion poster has the same id as the viewing user
     *
     * args:    $disc_id = id of discussion
     *          $user_id = id of user
     * returns: whether they have the same id
     */
    public static function same_as_poster($disc_id, $user_id)
    {
        return DiscussionModel::same_poster($disc_id, $user_id);
    }

    /* check if a user voted on a discussion
     *
     * args:    $disc_id = id of discussion
     *          $user_id = id of user
     * returns: whether the user voted
     */
    public static function has_voted($disc_id, $user_id)
    {
        return VoteModel::check_voted($disc_id, $user_id);
    }

    /* check if a user responded to a discussion
     *
     * args:    $disc_id = id of discussion
     *          $user_id = id of user
     * returns: whether the user responded
     */
    public static function has_responded($disc_id, $user_id)
    {
        return ResponseModel::user_response($disc_id, $user_id);
    }

    /* check if a user voted on a discussion (phase specific)
     *
     * args:    $disc_id = id of discussion
     *          $user_id = id of user
     *          $phase = current phase of the discussion
     * returns: whether the user voted during a specified phase
     */
    public static function check_voted($disc_id, $user_id, $phase)
    {
        return VoteModel::user_vote($disc_id, $user_id, $phase);
    }

    /* flash notice message for notice view and perform redirect
     *
     * args:    $msg = message to display
     * returns: redirect to notice view
     */
    public static function notice_msg($msg)
    {
        session()->flash('notice', $msg);

        return redirect('notice');
    }

    /* get stats for bottom page display
     *
     * args:    none
     * returns: array containing counts
     */
    public static function get_stats()
    {
        $content = array();

        $uc = DB::select('SELECT COUNT(*) AS count FROM users');
        $ud = DB::select('SELECT COUNT(*) AS count FROM discussions');
        $rc = DB::select('SELECT COUNT(*) AS count FROM responses');
        $vc = DB::select('SELECT COUNT(*) AS count FROM votes');

        $content['user_count'] = $uc;
        $content['discussion_count'] = $ud;
        $content['response_count'] = $rc;
        $content['vote_count'] = $vc;

        return $content;
    }

    /* covert unix time stamp to time since string
     *
     * args:    $datetime = unix time stamp
     * returns: time since string
     */
    public static function tm($datetime, $full = false)
    {
        $datetime = '@' . $datetime;
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v)
        {
            if($diff->$k)
            {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            }
            else
            {
                unset($string[$k]);
            }
        }

        if(!$full)
        {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
