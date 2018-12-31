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

class Discussion extends Controller
{
    public function disc_view($id)
    {
        $q = 'SELECT * FROM discussions WHERE id = ?';
        $a = [$id];

        if($select = DB::select($q, $a))
        {
            $content = Common::get_stats();

            $phase = $select[0]->current_phase;
            $select[0]->post_date = Common::tm($select[0]->post_date);

            if($select[0]->for_change > 0)
            {
                $select[0]->for_change = '+' . $select[0]->for_change;
            }

            if($select[0]->against_change > 0)
            {
                $select[0]->against_change = '+' . $select[0]->against_change;
            }

            $for = DB::select('SELECT * FROM responses WHERE proposition = ?
                AND opinion = "for" ORDER BY date ASC', [$id]);

            $against = DB::select('SELECT * FROM responses WHERE proposition = ?
                AND opinion = "against" ORDER BY date ASC', [$id]);

            for($i = 0; $i < count($for); $i++)
            {
                $for[$i]->date = Common::tm($for[$i]->date);
            }

            for($i = 0; $i < count($against); $i++)
            {
                $against[$i]->date = Common::tm($against[$i]->date);
            }

            $content['next_phase'] = $this->get_changing_msg($select[0]);
            $content['can_reply'] = Common::check_can_reply($id);
            $content['can_vote'] = Common::check_can_vote($id, $phase);
            $content['discussion'] = $select[0];
            $content['for'] = $for;
            $content['against'] = $against;
            $content['action'] = $this->get_user_action($id);

            $view = view('discussion_view')->with('content', $content);
        }
        else
        {
            session()->flash('notice', 'Invalid ID!');

            $view = redirect('notice');
        }

        return $view;
    }

    private function get_user_action($id)
    {
        $action = array();

        if(Auth::check())
        {
            $pre_vote = DB::select('SELECT opinion FROM votes
                WHERE proposition = ? AND
                phase = "pre-argument" AND user_id = ? ',
                [$id, Auth::user()->id]);

            $post_vote = DB::select('SELECT opinion FROM votes
                WHERE proposition = ? AND
                phase = "post-argument" AND user_id = ? ',
                [$id, Auth::user()->id]);

            $resp = DB::select('SELECT opinion FROM responses
                WHERE proposition = ? AND user_id = ? ',
                [$id, Auth::user()->id]);

            if($pre_vote)
            {
                $pre_vote = $pre_vote[0]->opinion;

                $action['did'] = 'you voted in the pre-argument phase: ';
                $action['res'] = $pre_vote;
            }

            if($post_vote)
            {
                $post_vote = $post_vote[0]->opinion;

                $action['did'] = 'you voted in the post-argument phase: ';
                $action['res'] = $post_vote;
            }

            if($resp)
            {
                $resp = $resp[0]->opinion;

                $action['did'] = 'you responded to the proposition: ';
                $action['res'] = $resp;
            }
        }

        return $action;
    }

    private function get_changing_msg($select)
    {
        switch($select->current_phase)
        {
            case('pre-argument'):
                $next_phase = $select->pa_end_date - time();
                $nm = 'argument';
                $has_next = 1;
                break;
            case('argument'):
                $next_phase = $select->a_end_date - time();
                $nm = 'post-argument';
                $has_next = 1;
                break;
            case('post-argument'):
                $next_phase = $select->v_end_date - time();
                $nm = 'ending';
                $has_next = 1;
            default:
                $has_next = 0;
                break;
        }

        if($has_next)
        {
            if($next_phase < 0)
            {
                $msg = 'changing soon...';
            }
            else
            {
                $next_phase = number_format($next_phase / 60 / 60, 2);

                $msg = $nm . ' phase in ' . $next_phase . ' hours';
            }
        }
        else
        {
            $msg = 'finished';
        }

        return $msg;
    }

    public function disc_sub_view()
    {
        if(!Auth::check())
        {
            return Common::notice_msg('Not logged in!');
        }
        else
        {
            return view('discussion_submit');
        }
    }

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

        $pa_end_date = time() + $this->to_unix($request->pa);
        $a_end_date = $pa_end_date + $this->to_unix($request->a);
        $v_end_date = $a_end_date + $this->to_unix($request->v);

        $insert = DB::insert(
            'INSERT INTO discussions (proposition, argument, reply_count,' .
            'pa_vote_count, pv_vote_count, post_date, user_name, user_id, ' .
            'vote_count, current_phase,' .
            'pa_end_date, a_end_date, v_end_date, pa_for, pa_against,' .
            'pa_undecided, pv_for, pv_against, pa_for_per, pa_against_per,' .
            'pa_undecided_per, pv_for_per, pv_against_per, for_change,' .
            'against_change, winner, recent_action)' .
            'VALUES (?, ?, 0,' .
            '0, 0, ?, ?, ?, ' .
            '0, "pre-argument",' .
            '?, ?, ?, 0, 0,' .
            '0, 0, 0, 0, 0,' .
            '0, 0, 0, 0,' .
            '0, "N/A", ?)',
            [strip_tags($request->prop), strip_tags($request->arg),
            time(), Auth::user()->user_name, Auth::user()->id,
            $pa_end_date, $a_end_date, $v_end_date,
            time()]);

        return Common::notice_msg('Discussion submitted!');
    }

    private function to_unix($type)
    {
        switch($type)
        {
            case "1hour": $time = 60 * 60; break;
            case "6hours": $time = 60 * 60 * 6; break;
            case "1day": $time = 60 * 60 * 24; break;
            case "3days": $time = 60 * 60 * 24 * 3; break;
            default: $time = 60 * 60 * 6; break;
        }

        return $time;
    }
}
