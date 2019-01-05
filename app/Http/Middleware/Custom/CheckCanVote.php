<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Http\Controllers\Common;

use Closure;
use Auth;

class CheckCanVote
{
    public function handle($request, Closure $next)
    {
        if(!$this->check_can_vote($request->id, $request->phase))
        {
            return Common::notice_msg('Cannot vote!');
        }

        return $next($request);
    }

    public static function check_can_vote($disc_id, $phase)
    {
        if(!Auth::check())
        {
            return 0;
        }

        $can_vote = 1;
        $user_id = Auth::user()->id;

        $same = Common::same_as_poster($disc_id, $user_id);
        $has_resp = Common::has_responded($disc_id, $user_id);
        $not_pre = $phase !== 'pre-argument';
        $not_post = $phase !== 'post-argument';

        if($phase === 'post-argument')
        {
            $cant_vote = 1;

            if(Common::check_voted($disc_id, $user_id, 'pre-argument'))
            {
                $cant_vote = 0;
            }

            if(Common::check_voted($disc_id, $user_id, 'post-argument'))
            {
                $cant_vote = 1;
            }
        }
        else
        {
            $cant_vote = 0;

            if(Common::check_voted($disc_id, $user_id, 'pre-argument'))
            {
                $cant_vote = 1;
            }
        }

        if($same || $has_resp || $cant_vote || ($not_pre && $not_post))
        {
            $can_vote = 0;
        }

        return $can_vote;
    }
}
