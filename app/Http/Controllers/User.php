<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;

use beevrr\Http\Controllers\Controller;
use beevrr\Http\Controllers\Common;
use beevrr\Models\ActivityModel;

use Auth;
use Redirect;
use Validator;

class User extends Controller
{
    /* mobile login
     *
     * args:    none
     *
     * returns: json response
     */
    public function login()
    {
        $auth = ['user_name' => request('user_name'), 'password' =>
            request('password')];

        if(Auth::attempt($auth))
        {
            $user = Auth::user();
            $success['token'] =  $user->createToken('beevrr')->accessToken;

            return response()->json(['status' => 'success',
                'auth' => $success], 200);
        }
        else
        {
            return response()->json(['status' => 'failure'], 200);
        }
    }

    /* mobile register
     *
     * args:    none
     *
     * returns: json response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => ['required', 'min:3', 'max:30', 'alpha_num',
                'string', 'unique:users'],
            'password' => ['required', 'min:3', 'string', 'same:passwordc'],
            'passwordc' => ['required', 'min:3', 'string', 'same:password'],
        ]);

        if($validator->fails())
        {
            return response()->json(['status'=>'failure'], 200);
        }

        User::create([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'score' => 0,
            'total_responses' => 0,
            'active_responses' => 0,
            'total_votes' => 0,
            'active_votes' => 0,
            'total_discussions' => 0,
            'active_discussions' => 0,
            'bio' => '',
        ]);

        return response()->json(['status' => 'success'], 200);
    }

    /* mobile logout
     *
     * args:    none
     *
     * returns: json response
     */
    public function logout()
    {
        if(Auth::check())
        {
            Auth::logout();
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'failure'], 200);
    }

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
    public function user_info($user_id, $option, $page = 0, Request $request)
    {
        $validator = Validator::make([$option], array(
            ['in:tot_res,act_res,tot_vot,act_vot,tot_dis,act_dis,act'],));

        if($validator->fails())
        {
            return Common::mobile_or_msg($request, false, 'Invalid option!');
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
            if($request['mobile'])
            {
                $content['status'] = 'end_pagination';

                return response()->json($content, 200);
            }
            else
            {
                return Redirect::route('user-info', [$user_id, $option]);
            }
        }

        if($request['mobile'])
        {
            $pg_route = 'mobile-page-ui';
        }
        else
        {
            $pg_route = 'page-ui';
        }

        $content = Common::get_stats();
        $content['user'] = $user[0];
        $content['title'] = $title;
        $content['activities'] = $activities;

        $l = route($pg_route, array(
            'id' => $user_id,
            'option' => $option,
            'p' => $page - 1,));

        $r = route($pg_route, array(
            'id' => $user_id,
            'option' => $option,
            'p' => $page + 1,));

        $content['pagination'] = Common::get_pagination_next($l, $r, $page);

        if($request['mobile'])
        {
            $content['status'] = 'success';

            return response()->json($content, 200);
        }
        else
        {
            return view('user_info')->with('content', $content);
        }
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
                $act = 0;
                $bet = [5, 6];
                break;
            case 'act_res':
                $get['title'] = 'active responses';
                $act = 1;
                $bet = [5, 6];
                break;
            case 'tot_vot':
                $get['title'] = 'total votes';
                $act = 0;
                $bet = [0, 4];
                break;
            case 'act_vot':
                $get['title'] = 'active votes';
                $act = 1;
                $bet = [0, 4];
                break;
            case 'tot_dis':
                $get['title'] = 'total discussions';
                $act = 0;
                $bet = [7];
                break;
            case 'act_dis':
                $get['title'] = 'active discussions';
                $act = 1;
                $bet = [7];
                break;
            default:
                $get['title'] = 'full activity';
                $act = 0;
                $bet = [];
                break;
        }

        $get['model'] = ActivityModel::get_activities($user_id,
            $act, $bet, $offset, $pagination);

        return $get;
    }
}
