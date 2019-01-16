<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use beevrr\Http\Middleware\Custom\CheckCanVote;
use beevrr\Http\Middleware\Custom\CheckCanRespond;
use beevrr\Http\Controllers\Common;
use beevrr\Models\DiscussionModel;
use beevrr\Models\ResponseModel;
use beevrr\Models\ActivityModel;
use beevrr\Models\VoteModel;
use beevrr\Models\LikesDiscModel;
use beevrr\User;

use Validator;
use Auth;

class Discussion extends Controller
{
    /* prepare discussion for view
     *
     * args:    $disc_id = id of discussion
     * returns: if invalid discussion: notice redirect
     *          if valid: view with discussion info
     */
    public function disc_view($disc_id, Request $request)
    {
        $discussion = DiscussionModel::select_from($disc_id);
        $phase = $discussion->current_phase;

        $this->add_change_symbol($discussion);

        $content = Common::get_stats();
        $content['next_phase'] = $this->get_changing_message($discussion);
        $content['action'] = $this->get_user_action($disc_id);
        $content['can_reply'] = CheckCanRespond::check_can_respond($disc_id);
        $content['can_vote'] = CheckCanVote::check_can_vote($disc_id, $phase);
        $content['f'] = ResponseModel::disc_responses('for', $disc_id);
        $content['a'] = ResponseModel::disc_responses('against', $disc_id);
        $content['liked'] = $this->get_like_text($disc_id);

        if ($request['mobile'])
        {
            $content['status'] = 'success';

            return response()->json($content, 200);
        }
        else
        {
            $content['discussion'] = $discussion;

            return view('discussion_view')->with('content', $content);
        }
    }

    /* check if user is logged in and dispay discussion submision form
     *
     * args:    none
     * returns: if logged in: user discussion form redirect
     *          if not: notice redirect
     */
    public function disc_sub_view()
    {
        return view('discussion_submit');
    }

    /* post discussion based on form input
     *
     * args:    $request = post request
     * returns: notice redirect
     */
    public function disc_sub_post(Request $request)
    {
        $captcha = Validator::make(Input::all(), array(
            'captcha' => 'required|captcha',));

        if($captcha->fails() && !$request['mobile'])
        {
            return Common::notice_msg('Bad CAPTCHA!');
        }

        $validator = Validator::make(Input::all(), array(
            'prop' => ['required', 'min:10', 'max:250', 'string'],
            'arg' => ['required', 'min:10', 'max:40000', 'string'],
            'pa' => ['required', 'in:1hour,6hours,1day,3days'],
            'a' => ['required', 'in:1hour,6hours,1day,3days'],
            'v' => ['required', 'in:1hour,6hours,1day,3days'],));

        if($validator->fails())
        {
            return Common::mobile_or_msg($request, false, 'Invalid input!');
        }

        $time = time();
        $pa = time() + $this->to_unix($request->pa);
        $a = $pa + $this->to_unix($request->a);
        $v = $a + $this->to_unix($request->v);
        $prop = strip_tags($request->prop);
        $arg = strip_tags($request->arg);

        DiscussionModel::insert($prop, $arg, $pa, $a, $v, $time);
        User::update_stat('disc');

        return Common::mobile_or_msg($request, true, 'Discussion submitted!');
    }

    /* check whether the user as performed any action relating to a discussion
     *
     * args:    $disc_id = id of discussion
     * returns: array containing action and description or an empty array
     */
    private function get_user_action($disc_id)
    {
        $action = array();

        if(!Auth::check())
        {
            return $action;
        }

        $user_id = Auth::user()->id;

        $pre_vote = VoteModel::user_vote($disc_id, $user_id, 'pre-argument');
        $post_vote = VoteModel::user_vote($disc_id, $user_id, 'post-argument');
        $response = ResponseModel::user_response($disc_id, $user_id);

        if($pre_vote)
        {
            $action['did'] = 'you voted in the pre-argument phase: ';
            $action['res'] = $pre_vote->opinion;
        }

        if($post_vote)
        {
            $action['did'] = 'you voted in the post-argument phase: ';
            $action['res'] = $post_vote->opinion;
        }

        if($response)
        {
            $action['did'] = 'you responded to the discussion: ';
            $action['res'] = $response->opinion;
        }

        return $action;
    }

    /* get changing message for discussion based on phase
     *
     * args:    $discussion = discussion array
     * returns: message to be displayed
     */
    private function get_changing_message($discussion)
    {
        $message = 'finished';

        switch($discussion->current_phase)
        {
            case('pre-argument'):
                $until = $discussion->pa_end_date - time();
                $phase = 'argument';
                $has_next = 1;
                break;
            case('argument'):
                $until = $discussion->a_end_date - time();
                $phase = 'post-argument';
                $has_next = 1;
                break;
            case('post-argument'):
                $until = $discussion->v_end_date - time();
                $phase = 'ending';
                $has_next = 1;
                break;
            default:
                $has_next = 0;
                break;
        }

        if($has_next)
        {
            if($until < 0)
            {
                $message = 'changing soon...';
            }
            else
            {
                $until = number_format($until / 60 / 60, 2);
                $message = $phase . ' phase in ' . $until . ' hours';
            }
        }

        return $message;
    }

    /* convert time post value to seconds
     *
     * args:    $type = type taken from discussion post form
     * returns: unix time conversion
     */
    private function to_unix($type)
    {
        switch($type)
        {
            case '1hour':
                $time = config('global.an_hour'); /* for debugging */
                break;
            case '6hours':
                $time = 60 * 60 * 6;
                break;
            case '1day':
                $time = 60 * 60 * 24;
                break;
            default:
                $time = 60 * 60 * 24 * 3;
                break;
        }

        return $time;
    }

    /* prepare for or against change for display
     *
     * args:    $discussion = discussion array
     * returns: none
     */
    private function add_change_symbol($discussion)
    {
        if($discussion->for_change > 0)
        {
            $discussion->for_change = '+' . $discussion->for_change;
        }

        if($discussion->against_change > 0)
        {
            $discussion->against_change = '+' . $discussion->against_change;
        }
    }

    /* toggle like for discussion
     *
     * args:    $disc_id = discussion id
     * returns: notice
     */
    public function disc_like($disc_id)
    {
        if(LikesDiscModel::check_liked($disc_id))
        {
            DiscussionModel::make_like($disc_id, 0);
            LikesDiscModel::remove_like($disc_id);
        }
        else
        {
            DiscussionModel::make_like($disc_id, 1);
            LikesDiscModel::insert_like($disc_id);
        }

        return Common::notice_msg('Discussion liked!');
    }

    /* prepare message for display based on like
     *
     * args:    $disc_id = discussion id
     * returns: "liked" string
     */
    private function get_like_text($disc_id)
    {
        if(!Auth::check())
        {
            return '';
        }

        if(LikesDiscModel::check_liked($disc_id))
        {
            return '[unlike]';
        }
        else
        {
            return '[like]';
        }
    }
}
