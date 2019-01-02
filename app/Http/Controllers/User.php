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
    public function user_view($user_id)
    {
        if(count($user = $this->check_valid_user($user_id)) === 0)
        {
            return Common::notice_msg('Invalid ID!');
        }

        $content = Common::get_stats();
        $content['user'] = $user;

        return view('user_view')->with('content', $content);;
    }

    public function user_info($user_id, $option, $page = 0)
    {
        if(count($user = $this->check_valid_user($user_id)) === 0)
        {
            return Common::notice_msg('Invalid ID!');
        }

        $validator = Validator::make([$option], array(
            ['in:tot_res,act_res,tot_vot,act_vot,tot_dis,act_dis,act'],));

        if($validator->fails())
        {
            return Common::notice_msg('Invalid option!');
        }

        $pagination = config('global.pagination');
        $offset = Common::get_offset($page);

        switch($option)
        {
            case 'tot_res':
                $title = 'total responses';
                $get = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [5, 6])
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'act_res':
                $title = 'active responses';
                $get = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [5, 6])
                    ->where('is_active', 1)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'tot_vot':
                $title = 'total votes';
                $get = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [0, 4])
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'act_vot':
                $title = 'active votes';
                $get = ActivityModel::where('user_id', $user_id)
                    ->whereBetween('action_type', [0, 4])
                    ->where('is_active', 1)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'tot_dis':
                $title = 'total discussions';
                $get = ActivityModel::where('user_id', $user_id)
                    ->where('action_type', 7)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            case 'act_dis':
                $title = 'active discussions';
                $get = ActivityModel::where('user_id', $user_id)
                    ->where('action_type', 7)
                    ->where('is_active', 1)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
            default:
                $title = 'full activity';
                $get = ActivityModel::where('user_id', $user_id)
                    ->orderBy('date', 'DESC')
                    ->skip($offset)
                    ->take($pagination)
                    ->get();
                break;
        }

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

    private function check_valid_user($user_id)
    {
        if($select = \beevrr\User::select('*')->where('id', $user_id)->get())
        {
            return $select;
        }

        return 0;
    }
}
