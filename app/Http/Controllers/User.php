<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;

use beevrr\Http\Controllers\Common;
use beevrr\Models\ActivityModel;

use Auth;
use Redirect;
use Validator;

class User extends Controller
{
    /* view a user's page
     *
     * args:    $user_id = id of user
     * returns: if error: notice redirect
     *          else: user page
     */
    public function user_view($user_id)
    {
        $content = Common::get_stats();
        $content['user'] = \beevrr\User::select_from($user_id);

        return view('user_view')->with('content', $content);
    }

    /* prepare user info page to display activities
     *
     * args:    $user_id = id of user to view
     *          $option = valid option (total or active responses, votes, etc)
     *          $page = pagination
     * returns: if error: notice redirect
     *          else: user info page with activities
     */
    public function user_info($user_id, $option, $page = 0)
    {
        $validator = Validator::make([$option], array(
            ['in:tot_res,act_res,tot_vot,act_vot,tot_dis,act_dis,act'],));

        if($validator->fails())
        {
            return Common::notice_msg('Invalid option!');
        }

        $user = \beevrr\User::select_from($user_id);;

        $pagination = config('global.pagination');
        $offset = Common::get_offset($page);

        $act = $this->activity_model($option, $user_id, $offset, $pagination);
        $title = $act['title'];
        $get = $act['model'];

        $activities = $this->get_user_activity($get);

        if(Common::pagination_redirect($activities, $page))
        {
            return Redirect::route('user-info', [$user_id, $option]);
        }

        $content = Common::get_stats();
        $content['user'] = $user[0];
        $content['title'] = $title;
        $content['activities'] = $activities;

        $l = route('page-ui', array(
            'id' => $user_id,
            'option' => $option,
            'p' => $page - 1,));

        $r = route('page-ui', array(
            'id' => $user_id,
            'option' => $option,
            'p' => $page + 1,));

        $content['pagination'] = Common::get_pagination_next($l, $r, $page);

        return view('user_info')->with('content', $content);
    }

    /* convert user activities in db to readable strings
     *
     * args:    $activities = array of user activities
     * returns: array containing activities as strings for viewing
     */
    private function get_user_activity($activities)
    {
        $activity = array();

        foreach($activities as $a)
        {
            $item = array();
            $type = $a->action_type;

            switch(true)
            {
                case($type >= 0 && $type <= 4):
                    $thing = 'voted in discussion ';
                    break;
                case($type === 5 || $type === 6):
                    $thing = 'responded to discussion ';
                    break;
                default:
                    $thing = 'created discussion ';
                    break;
            }

            switch(true)
            {
                case($type === 0 || $type === 3 || $type === 5):
                    $type = '(for)';
                    break;
                case($type === 1 || $type === 4 || $type === 6):
                    $type = '(against)';
                    break;
                case($type === 2):
                    $type = '(undecided)';
                    break;
                default:
                    $type = '';
                    break;
            }

            $item['thing'] = $thing;
            $item['type'] = $type;
            $item['prop'] = $a->proposition;
            $item['date'] = Common::tm($a->date);

            $activity[] = $item;
        }

        return $activity;
    }

    /* check if user exists
     *
     * args:    $user_id = id of user
     * returns: if valid: user array
     *          if not: 0
     */
    private function check_valid_user($user_id)
    {
        if($select = \beevrr\User::select('*')->where('id', $user_id)->get())
        {
            return $select;
        }

        return 0;
    }

    /* get user activities from database
     *
     * args:    $option = the type of activity set to return
     *          $user_id = id of the user
     *          $offset = pagination offset
     *          $pagination = limit
     * returns: array containing title and retrieved model
     */
    private function activity_model($option, $user_id, $offset, $pagination)
    {
        $get = array();

        switch($option)
        {
            case 'tot_res':
                $get['title'] = 'total responses';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [5, 6])
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'act_res':
                $get['title'] = 'active responses';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [5, 6])
                    ->where('is_active', 1)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'tot_vot':
                $get['title'] = 'total votes';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [0, 4])
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'act_vot':
                $get['title'] = 'active votes';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [0, 4])
                    ->where('is_active', 1)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'tot_dis':
                $get['title'] = 'total discussions';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->where('action_type', 7)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'act_dis':
                $get['title'] = 'active discussions';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->where('action_type', 7)
                    ->where('is_active', 1)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            default:
                $get['title'] = 'full activity';
                $get['model'] = ActivityModel::where('user_id', $user_id)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
        }

        return $get;
    }
}
