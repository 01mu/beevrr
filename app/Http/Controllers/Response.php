<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use beevrr\Http\Controllers\Common;
use beevrr\Models\ResponseModel;
use beevrr\Models\DiscussionModel;
use beevrr\Models\ActivityModel;
use beevrr\User;

use Validator;
use Auth;

class Response extends Controller
{
    public function resp_view($disc_id)
    {
        if(!Common::check_exists($disc_id))
        {
            return Common::notice_msg('Invalid ID!');
        }

        if(!Common::check_can_reply($disc_id))
        {
            return Common::notice_msg('Cannot respond!');
        }

        return view('reply_submit')->with('id', $disc_id);
    }

    public function resp_post($disc_id, Request $request)
    {
        if(!Common::check_exists($disc_id))
        {
            return Common::notice_msg('Invalid ID!');
        }

        if(!Common::check_can_reply($disc_id))
        {
            return Common::notice_msg('Cannot respond!');
        }

        $captcha = Validator::make(Input::all(), array(
            'captcha' => 'required|captcha',));

        if($captcha->fails())
        {
            return Common::notice_msg('Bad CAPTCHA!');
        }

        $validator = Validator::make(Input::all(), array(
            'resp' => ['required', 'min:10', 'max:40000', 'string'],
            'type' => ['required', 'in:for,against'],));

        if($validator->fails())
        {
            return Common::notice_msg('Invalid input!');
        }

        $time = time();
        $type = $request->type;

        $user_id = Auth::user()->id;
        $user_name = Auth::user()->user_name;

        $discussion_insert = new ResponseModel;
        $discussion_insert->response = strip_tags($request->resp);
        $discussion_insert->proposition = $disc_id;
        $discussion_insert->user_id = $user_id;
        $discussion_insert->user_name = $user_name;
        $discussion_insert->opinion = $type;
        $discussion_insert->date = $time;
        $discussion_insert->save();

        $activity_insert = new ActivityModel;
        $activity_insert->user_id = $user_id;
        $activity_insert->user_name = $user_name;
        $activity_insert->action_type = $this->get_activity_type($type);
        $activity_insert->proposition = $disc_id;
        $activity_insert->date = $time;
        $activity_insert->save();

        $discussion_update = DiscussionModel::find($disc_id);
        $discussion_update->reply_count += 1;
        $discussion_update->recent_action = $time;
        $discussion_update->save();

        $user_update = User::find($user_id);
        $user_update->total_responses += 1;
        $user_update->active_responses += 1;
        $user_update->save();

        return Common::notice_msg('Response submitted!');
    }

    private function get_activity_type($type)
    {
        switch($type)
        {
            case 'for':
                $tp = 5;
                break;
            default:
                $tp = 6;
                break;
        }

        return $tp;
    }
}
