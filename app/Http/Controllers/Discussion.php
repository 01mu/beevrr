<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use beevrr\Http\Controllers\Common;
use beevrr\Models\DiscussionModel;
use beevrr\Models\ResponseModel;
use beevrr\Models\ActivityModel;
use beevrr\Models\VoteModel;
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
    public function disc_view($disc_id)
    {
        if(count($select = $this->check_valid_disc($disc_id)) === 0)
        {
            session()->flash('notice', 'Invalid ID!');

            return redirect('notice');
        }

        $discussion = $select[0];
        $phase = $discussion->current_phase;

        $for = ResponseModel::select('*')
            ->where('proposition', $disc_id)
            ->where('opinion', 'for')
            ->orderBy('date', 'ASC')
            ->get();

        $against = ResponseModel::select('*')
            ->where('proposition', $disc_id)
            ->where('opinion', 'against')
            ->orderBy('date', 'ASC')
            ->get();

        $discussion->post_date = Common::tm($discussion->post_date);
        $this->add_change_symbol($discussion);

        Common::fix_time($for, 1);
        Common::fix_time($against, 1);

        $content = Common::get_stats();
        $content['next_phase'] = $this->get_changing_message($discussion);
        $content['action'] = $this->get_user_action($disc_id);
        $content['can_reply'] = Common::check_can_reply($disc_id);
        $content['can_vote'] = Common::check_can_vote($disc_id, $phase);
        $content['discussion'] = $discussion;
        $content['for'] = $for;
        $content['against'] = $against;

        return view('discussion_view')->with('content', $content);
    }

    /* check if user is logged in and dispay discussion submision form
     *
     * args:    none
     * returns: if logged in: user discussion form redirect
     *          if not: notice redirect
     */
    public function disc_sub_view()
    {
        if(!Auth::check())
        {
            return Common::notice_msg('Not logged in!');
        }

        return view('discussion_submit');
    }

    /* post discussion based on form input
     *
     * args:    $request = post request
     * returns: notice redirect
     */
    public function disc_sub_post(Request $request)
    {
        if(!Auth::check())
        {
            return Common::notice_msg('Not logged in!');
        }

        $captcha = Validator::make(Input::all(), array(
            'captcha' => 'required|captcha',));

        if($captcha->fails())
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
            return Common::notice_msg('Invalid input!');
        }

        $time = time();
        $user_id = Auth::user()->id;
        $user_name = Auth::user()->user_name;
        $pa_end_date = time() + $this->to_unix($request->pa);
        $a_end_date = $pa_end_date + $this->to_unix($request->a);
        $v_end_date = $a_end_date + $this->to_unix($request->v);
        $prop = strip_tags($request->prop);
        $arg = strip_tags($request->arg);

        DiscussionModel::insert($prop, $arg, $user_name, $user_id, $pa_end_date,
            $a_end_date, $v_end_date, $time);

        User::update_stat($user_id, 'disc');

        return Common::notice_msg('Discussion submitted!');
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

        $pre_vote = VoteModel::select('opinion')
            ->where('proposition', $disc_id)
            ->where('phase', 'pre-argument')
            ->where('user_id', $user_id)
            ->get()
            ->first();

        $post_vote = VoteModel::select('opinion')
            ->where('proposition', $disc_id)
            ->where('phase', 'post-argument')
            ->where('user_id', $user_id)
            ->get()
            ->first();

        $response = ResponseModel::select('opinion')
            ->where('proposition', $disc_id)
            ->where('user_id', $user_id)
            ->get()
            ->first();

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

    /* check if the discussion is valid
     *
     * args:    $disc_id = id of discussion
     * returns: if not valid: 0
     *          if valid: discussion array
     */
    private function check_valid_disc($disc_id)
    {
        if($select = DiscussionModel::select('*')->where('id', $disc_id)->get())
        {
            return $select;
        }

        return 0;
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
}
