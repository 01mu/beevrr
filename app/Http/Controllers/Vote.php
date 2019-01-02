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
        if(!Common::check_exists($disc_id))
        {
            return Common::notice_msg('Invalid ID!');
        }

        if(!Common::check_can_vote($disc_id, $phase))
        {
            return Common::notice_msg('Cannot vote!');
        }

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
        if(!Common::check_exists($disc_id))
        {
            return Common::notice_msg('Invalid ID!');
        }

        if(!Common::check_can_vote($disc_id, $phase))
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

        $time = time();

        $user_id = Auth::user()->id;
        $user_name = Auth::user()->user_name;

        $discussion_update = DiscussionModel::find($disc_id);
        $discussion_update->vote_count += 1;
        $discussion_update->recent_action = $time;
        $discussion_update->save();

        $this->update_vote_info($disc_id, $phase);

        $vote_insert = new VoteModel;
        $vote_insert->opinion = $request->v;
        $vote_insert->proposition = $disc_id;
        $vote_insert->user_id = $user_id;
        $vote_insert->user_name = $user_name;
        $vote_insert->phase = $phase;
        $vote_insert->date = $time;
        $vote_insert->save();

        $act_type = $this->get_activity_type($phase);
        Common::activity($user_id, $user_name, $act_type, $disc_id, $time);

        $user_update = User::find($user_id);
        $user_update->total_votes += 1;
        $user_update->active_votes += 1;
        $user_update->save();

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
            $discussion_update = DiscussionModel::find($disc_id);
            $discussion_update->pa_vote_count += 1;

            switch(Input::get('v'))
            {
                case 'for':
                    $discussion_update->pa_for += 1;
                    break;
                case 'against':
                    $discussion_update->pa_against += 1;
                    break;
                default:
                    $discussion_update->pa_undecided += 1;
                    break;
            }

            $discussion_update->save();

            $for_count = DiscussionModel::select('pa_for')
                ->where('id', $disc_id)
                ->get()
                ->first()
                ->pa_for;

            $aga_count = DiscussionModel::select('pa_against')
                ->where('id', $disc_id)
                ->get()
                ->first()
                ->pa_against;

            $und_count = DiscussionModel::select('pa_undecided')
                ->where('id', $disc_id)
                ->get()
                ->first()
                ->pa_undecided;

            $for_per = $for_count / $discussion_update->pa_vote_count * 100;
            $aga_per = $aga_count / $discussion_update->pa_vote_count * 100;
            $und_per = $und_count / $discussion_update->pa_vote_count * 100;

            $discussion_update->pa_for_per = number_format($for_per, 2);
            $discussion_update->pa_against_per = number_format($aga_per, 2);
            $discussion_update->pa_undecided_per = number_format($und_per, 2);

            $discussion_update->save();
        }
        else
        {
            $discussion_update = DiscussionModel::find($disc_id);
            $discussion_update->pv_vote_count += 1;

            switch(Input::get('v'))
            {
                case 'for':
                    $discussion_update->pv_for += 1;
                    break;
                default:
                    $discussion_update->pv_against += 1;
                    break;
            }

            $discussion_update->save();

            $for_count = DiscussionModel::select('pv_for')
                ->where('id', $disc_id)
                ->get()
                ->first()
                ->pv_for;

            $aga_count = DiscussionModel::select('pv_against')
                ->where('id', $disc_id)
                ->get()
                ->first()
                ->pv_against;

            $pvfp = $for_count / $discussion_update->pv_vote_count * 100;
            $pvap = $aga_count / $discussion_update->pv_vote_count * 100;

            $discussion_update->pv_for_per = number_format($pvfp, 2);
            $discussion_update->pv_against_per = number_format($pvap);

            $discussion_update->save();

            $this->update_changes($discussion_update, $disc_id, $pvfp, $pvap);
        }
    }

    /* update opinion percentage point change for discussion
     *
     * args:    $discussion_update = model for last post-vote update
     *          $disc_id = id of discussion
     *          $pvfv = post-voting for percentage
     *          $pvag = post-voting against percentage
     * returns: none
     */
    private function update_changes($discussion_update, $disc_id, $pvfp, $pvap)
    {
        $pa_for_per = DiscussionModel::select('pa_for_per')
            ->where('id', $disc_id)
            ->get()
            ->first()
            ->pa_for_per;

        $pa_against_per = DiscussionModel::select('pa_against_per')
            ->where('id', $disc_id)
            ->get()
            ->first()
            ->pa_against_per;

        $fc = number_format($pvfp - $pa_for_per, 2);
        $ac = number_format($pvap - $pa_against_per, 2);

        $discussion_update->for_change = $fc
        $discussion_update->against_change = $ac;

        $discussion_update->save();
    }
}
