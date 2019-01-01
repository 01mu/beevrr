<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;

use beevrr\Http\Controllers\Common;

use Validator;
use DB;
use Auth;
use DateTime;

class Response extends Controller
{
    public function resp_view($id)
    {
        if(Common::check_exists($id))
        {
            if(!Common::check_can_reply($id))
            {
                $view = Common::notice_msg('Cannot post!');
            }
            else
            {
               $view = view('reply_submit')->with('id', $id);
            }
        }
        else
        {
            $view = Common::notice_msg('Invalid ID!');
        }

        return $view;
    }

    public function resp_post($id)
    {
        if(Common::check_exists($id))
        {
            if(!Common::check_can_reply($id))
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

            DB::insert(
                'INSERT INTO responses (response, proposition, user_id, ' .
                'user_name, opinion, date) ' .
                'VALUES (?, ?, ?,' .
                '?, ?, ?)',
                [strip_tags(Input::get('resp')), $id, Auth::user()->id,
                Auth::user()->user_name, Input::get('type'), time()]);

            DB::update(
                'UPDATE discussions SET reply_count = reply_count + 1,
                recent_action = ?
                WHERE id = ?', [time(), $id]);

            DB::update(
                'UPDATE users SET total_responses = total_responses + 1, ' .
                'active_responses = active_responses + 1 ' .
                'WHERE id = ?', [Auth::user()->id]);

            DB::insert('INSERT INTO activities (user_id, user_name, action_type,
                proposition, date, is_active) VALUES (?, ?, ?, ?, ?, 1)',
                [Auth::user()->id, Auth::user()->user_name,
                $this->get_activity_type(),  $id, time()]);

            return Common::notice_msg('Response submitted!');
        }
        else
        {
            return Common::notice_msg('Invalid ID!');
        }
    }

    private function get_activity_type()
    {
        switch(Input::get('type'))
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
