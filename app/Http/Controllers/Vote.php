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

class Vote extends Controller
{
    public function vote_view($phase, $id)
    {
        if(Common::check_exists($id))
        {
            $right_phase = $this->right_phase($id, $phase);
            $can_vote = Common::check_can_vote($id, $phase);

            if(!$right_phase || !$can_vote)
            {
                $view = Common::notice_msg('Cannot vote!');
            }
            else
            {
                $content = array();

                $content['phase'] = $phase;
                $content['id'] = $id;

                $view = view('vote_submit')->with('content', $content);
            }
        }
        else
        {
            $view = Common::notice_msg('Invalid ID!');
        }

        return $view;
    }

    public function vote_post($phase, $id)
    {
        if(Common::check_exists($id))
        {
            if(!Common::check_can_vote($id, $phase))
            {
                return Common::notice_msg('Cannot vote!');
            }

            $captcha = Validator::make(Input::all(), array(
                'captcha' => 'required|captcha',));

            if($captcha->fails())
            {
                return Common::notice_msg('Bad CAPTCHA!');
            }

            if($phase === 'pre-argument')
            {
                $validator = Validator::make(Input::all(), array(
                    'v' => ['required', 'in:for,against,undecided'],));
            }
            else
            {
                $validator = Validator::make(Input::all(), array(
                    'v' => ['required', 'in:for,against'],));
            }

            if($validator->fails())
            {
                return Common::notice_msg('Invalid input!');
            }

            DB::update(
                'UPDATE discussions SET vote_count = vote_count + 1,
                recent_action = ?
                WHERE id = ?', [time(), $id]);

            $this->update_vote_info($id, $phase);

            DB::insert(
                'INSERT INTO votes (proposition, user_id, user_name, ' .
                'opinion, date, phase) ' .
                'VALUES (?, ?, ?,' .
                '?, ?, ?)',
                [$id, Auth::user()->id, Auth::user()->user_name,
                Input::get('v'), time(), $phase]);

            DB::update(
                'UPDATE users SET total_votes = total_votes + 1, ' .
                'active_votes = active_votes + 1 ' .
                'WHERE id = ?', [Auth::user()->id]);

            DB::insert('INSERT INTO activities (user_id, user_name, action_type,
                proposition, date, is_active) VALUES (?, ?, ?, ?, ?, 1)',
                [Auth::user()->id, Auth::user()->user_name,
                $this->get_activity_type($phase),  $id, time()]);

           return Common::notice_msg('Vote submitted!');
        }
        else
        {
            return Common::notice_msg('Invalid ID!');
        }
    }

    private function get_activity_type($phase)
    {
        if($phase === 'pre-argument')
        {
            switch(Input::get('v'))
            {
                case 'for':
                    $tp = 0;
                    break;
                case 'against':
                    $tp = 1;
                    break;
                default:
                    $tp = 2;
                    break;
            }
        }
        else
        {
            switch(Input::get('v'))
            {
                case 'for':
                    $tp = 3;
                    break;
                default:
                    $tp = 4;
                    break;
            }
        }

        return $tp;
    }

    private function update_vote_info($id, $phase)
    {
        if($phase === 'pre-argument')
        {
            DB::update(
                'UPDATE discussions SET pa_vote_count = pa_vote_count + 1
                WHERE id = ?', [$id]);

            switch(Input::get('v'))
            {
                case 'for':
                    DB::update(
                        'UPDATE discussions
                        SET pa_for = pa_for + 1
                        WHERE id = ?', [$id]);
                    break;
                case 'against':
                    DB::update(
                        'UPDATE discussions
                        SET pa_against = pa_against + 1
                        WHERE id = ?', [$id]);
                    break;
                default:
                    DB::update(
                        'UPDATE discussions
                        SET pa_undecided = pa_undecided + 1
                        WHERE id = ?', [$id]);
                    break;
            }

            $vote_count = DB::select('SELECT pa_vote_count FROM discussions
                WHERE id = ?', [$id])[0]->pa_vote_count;

            $for_count = DB::select('SELECT pa_for
                FROM discussions
                WHERE id = ?', [$id])[0]->pa_for;

            $aga_count = DB::select('SELECT pa_against
                FROM discussions
                WHERE id = ?', [$id])[0]->pa_against;

            $und_count = DB::select('SELECT pa_undecided
                FROM discussions
                WHERE id = ?', [$id])[0]->pa_undecided;

            $for_per = $for_count / $vote_count * 100;
            $aga_per = $aga_count / $vote_count * 100;
            $und_per = $und_count / $vote_count * 100;

            DB::update(
                'UPDATE discussions SET pa_for_per = ?
                WHERE id = ?', [$for_per, $id]);

            DB::update(
                'UPDATE discussions SET pa_against_per = ?
                WHERE id = ?', [$aga_per, $id]);

            DB::update(
                'UPDATE discussions SET pa_undecided_per = ?
                WHERE id = ?', [$und_per, $id]);
        }
        else
        {
            DB::update(
                'UPDATE discussions SET pv_vote_count = pv_vote_count + 1
                WHERE id = ?', [$id]);

            switch(Input::get('v'))
            {
                case 'for':
                    DB::update(
                        'UPDATE discussions
                        SET pv_for = pv_for + 1
                        WHERE id = ?', [$id]);
                    break;
                default:
                    DB::update(
                        'UPDATE discussions
                        SET pv_against = pv_against + 1
                        WHERE id = ?', [$id]);
                    break;
            }

            $vote_count = DB::select('SELECT pv_vote_count FROM discussions
                WHERE id = ?', [$id])[0]->pv_vote_count;

            $for_count = DB::select('SELECT pv_for
                FROM discussions
                WHERE id = ?', [$id])[0]->pv_for;

            $aga_count = DB::select('SELECT pv_against
                FROM discussions
                WHERE id = ?', [$id])[0]->pv_against;

            $for_per = $for_count / $vote_count * 100;
            $aga_per = $aga_count / $vote_count * 100;

            DB::update(
                'UPDATE discussions SET pv_for_per = ?
                WHERE id = ?', [$for_per, $id]);

            DB::update(
                'UPDATE discussions SET pv_against_per = ?
                WHERE id = ?', [$aga_per, $id]);

            $pa_for_per = DB::select('SELECT pa_for_per
                FROM discussions
                WHERE id = ?', [$id])[0]->pa_for_per;

            $pa_against_per = DB::select('SELECT pa_against_per
                FROM discussions
                WHERE id = ?', [$id])[0]->pa_against_per;

            $pv_for_per = DB::select('SELECT pv_for_per
                FROM discussions
                WHERE id = ?', [$id])[0]->pv_for_per;

            $pv_against_per = DB::select('SELECT pv_against_per
                FROM discussions
                WHERE id = ?', [$id])[0]->pv_against_per;

            $for_change = $pv_for_per - $pa_for_per;
            $against_change = $pv_against_per - $pa_against_per;

            DB::update(
                'UPDATE discussions SET for_change = ?
                WHERE id = ?', [$for_change, $id]);

            DB::update(
                'UPDATE discussions SET against_change = ?
                WHERE id = ?', [$against_change, $id]);
        }
    }

    private function right_phase($disc_id, $arg_phase)
    {
        if($arg_phase !== 'pre-argument' && $arg_phase !== 'post-argument')
        {
            return 0;
        }
        else
        {
            $current_phase = DB::select('SELECT * FROM discussions
                WHERE id = ?', [$disc_id])[0]->current_phase;

            if($current_phase === $arg_phase)
            {
                return 1;
            }
            else
            {
                return 0;
            }
        }
    }
}
