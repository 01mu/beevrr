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
use beevrr\Models\VoteModel;
use beevrr\Models\DiscussionModel;
use beevrr\Models\ActivityModel;
use beevrr\User;

use Validator;
use Auth;

class Vote extends Controller
{
    /* dispay vote submision form
     *
     * args:    $phase = discussion's current phase
     *          $disc_id = id of discussion to vote on
     * returns: if error: notice redirect
     *          else: vote submission form
     */
    public function vote_view($phase, $disc_id)
    {
        $content = array();

        $content['phase'] = $phase;
        $content['id'] = $disc_id;

        return view('vote_submit')->with('content', $content);
    }

    /* post vote based on form input
     *
     * args:    $phase = discussion's current phase
     *          $disc_id = id of discussion to respond to
     *          $request = post request
     * returns: notice redirect
     */
    public function vote_post($phase, $disc_id, Request $request)
    {
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

        $time = time();
        $act_type = $this->get_activity_type($phase);

        $this->update_vote_info($disc_id, $phase);

        VoteModel::insert($request->v, $disc_id, $phase, $time);
        ActivityModel::insert($act_type, $disc_id, $time);
        DiscussionModel::update_disc($disc_id, 'vote', $time);
        User::update_stat('vote');

        return Common::notice_msg('Vote submitted!');
    }

    /* get type for activity insert based on vote post
     *
     * args:    $phase = discussion's current phase
     * returns: numeric type
     */
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

    /* update vote information for a given discussion
     *
     * args:    $disc_id = id of discussion
     *          $phase = discussion's current phase
     * returns: none
     */
    private function update_vote_info($disc_id, $phase)
    {
        if($phase === 'pre-argument')
        {
            $disc_up = DiscussionModel::find($disc_id);
            $disc_up->pa_vote_count += 1;

            switch(Input::get('v'))
            {
                case 'for':
                    $disc_up->pa_for += 1;
                    break;
                case 'against':
                    $disc_up->pa_against += 1;
                    break;
                default:
                    $disc_up->pa_undecided += 1;
                    break;
            }

            $for_per = $disc_up->pa_for / $disc_up->pa_vote_count * 100;
            $aga_per = $disc_up->pa_against / $disc_up->pa_vote_count * 100;
            $und_per = $disc_up->pa_undecided / $disc_up->pa_vote_count * 100;

            $disc_up->pa_for_per = number_format($for_per, 2);
            $disc_up->pa_against_per = number_format($aga_per, 2);
            $disc_up->pa_undecided_per = number_format($und_per, 2);

            $disc_up->save();
        }
        else
        {
            $disc_up = DiscussionModel::find($disc_id);
            $disc_up->pv_vote_count += 1;

            switch(Input::get('v'))
            {
                case 'for':
                    $disc_up->pv_for += 1;
                    break;
                default:
                    $disc_up->pv_against += 1;
                    break;
            }

            $pvfp = $disc_up->pv_for / $disc_up->pv_vote_count * 100;
            $pvap = $disc_up->pv_against / $disc_up->pv_vote_count * 100;

            $disc_up->pv_for_per = number_format($pvfp, 2);
            $disc_up->pv_against_per = number_format($pvap);

            $disc_up->save();

            $this->update_changes($disc_up, $disc_id, $pvfp, $pvap);
        }
    }

    /* update opinion percentage point change for discussion
     *
     * args:    $disc_up = model for last post-argument update
     *          $disc_id = id of discussion
     *          $pvfp = post-voting for percentage
     *          $pvap = post-voting against percentage
     * returns: none
     */
    private function update_changes($disc_up, $disc_id, $pvfp, $pvap)
    {
        $pa_for_per = $disc_up->pa_for_per;
        $pa_against_per = $disc_up->pa_against_per;

        $fc = number_format($pvfp - $pa_for_per, 2);
        $ac = number_format($pvap - $pa_against_per, 2);

        $disc_up->for_change = $fc;
        $disc_up->against_change = $ac;

        $disc_up->save();
    }
}
