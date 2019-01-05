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
    /* dispay response submision form
     *
     * args:    $disc_id = id of discussion to respond to
     * returns: if error: notice redirect
     *          else: reply submission form
     */
    public function resp_view($disc_id)
    {
        if(!Common::check_can_reply($disc_id))
        {
            return Common::notice_msg('Cannot respond!');
        }

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
        $opinion = $request->type;
        $act_type = $this->get_activity_type($opinion);
        $response = strip_tags($request->resp);

        ResponseModel::insert($response, $disc_id, $opinion, $time);
        ActivityModel::insert($act_type, $disc_id, $time);
        DiscussionModel::update_disc($disc_id, 'response', $time);
        User::update_stat('response');

        return Common::notice_msg('Response submitted!');
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
}
