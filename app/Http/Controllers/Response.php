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
use beevrr\Models\LikesRespModel;
use beevrr\User;

use Validator;
use Auth;

class Response extends Controller
{
    /* dispay response submision form
     *
     * args:    $disc_id = id of discussion to respond to
     * returns: if error: notice redirect
     *          else: reply submission form
     */
    public function resp_view($disc_id)
    {
        return view('reply_submit')->with('id', $disc_id);
    }

    /* post response based on form input
     *
     * args:    $disc_id = id of discussion to respond to
     *          $request = post request
     * returns: notice redirect
     */
    public function resp_post($disc_id, Request $request)
    {
        $captcha = Validator::make(Input::all(), array(
            'captcha' => 'required|captcha',));

        if($captcha->fails() && !$request['mobile'])
        {
            return Common::notice_msg('Bad CAPTCHA!');
        }

        $validator = Validator::make(Input::all(), array(
            'resp' => ['required', 'min:10', 'max:40000', 'string'],
            'type' => ['required', 'in:for,against'],));

        if($validator->fails())
        {
            return Common::mobile_or_msg($request, false, 'Invalid input!');
        }

        $time = time();
        $opinion = $request->type;
        $act_type = $this->get_activity_type($opinion);
        $response = strip_tags($request->resp);

        ResponseModel::insert($response, $disc_id, $opinion, $time);
        ActivityModel::insert($act_type, $disc_id, $time);
        DiscussionModel::update_disc($disc_id, 'response', $time);
        User::update_stat('response');

        return Common::mobile_or_msg($request, true, 'Response submitted!');
    }

    /* get type for activity input
     *
     * args:    $type = response's type
     * returns: numeric value
     */
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

    /* toggle like for response
     *
     * args:    $resp_id = response id
     * returns: notice
     */
    public function resp_like($resp_id, Request $request)
    {
        if(LikesRespModel::check_liked($resp_id))
        {
            ResponseModel::make_like($resp_id, 0);
            LikesRespModel::remove_like($resp_id);
        }
        else
        {
            ResponseModel::make_like($resp_id, 1);
            LikesRespModel::insert_like($resp_id);
        }

        return Common::mobile_or_msg($request, true, 'Response liked!');
    }

    /* prepare message for display based on like
     *
     * args:    $resp_id = response id
     * returns: "liked" string
     */
    public static function get_like_text($resp_id)
    {
        if(!Auth::check())
        {
            return '';
        }

        if(LikesRespModel::check_liked($resp_id))
        {
            return '[unlike]';
        }
        else
        {
            return '[like]';
        }
    }
}
