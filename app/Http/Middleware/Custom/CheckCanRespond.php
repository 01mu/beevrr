<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Http\Controllers\Common;
use beevrr\Models\DiscussionModel;

use Closure;
use Auth;

class CheckCanRespond
{
    public function handle($request, Closure $next)
    {
        if(!$this->check_can_respond($request->id))
        {
            return Common::notice_msg('Cannot respond!');
        }

        return $next($request);
    }

    /* check if a user can respond to a discussion
     *
     * args:    $disc_id = discussion id
     * returns: whether the user can respond
     */
    public static function check_can_respond($disc_id)
    {
        if(!Auth::check())
        {
            return 0;
        }

        $can_reply = 1;
        $user_id = Auth::user()->id;

        $same = Common::same_as_poster($disc_id, $user_id);
        $has_vote = Common::has_voted($disc_id, $user_id);
        $has_resp = Common::has_responded($disc_id, $user_id);

        $disc = DiscussionModel::select_from($disc_id);
        $is_arg_phase = $disc->current_phase === 'argument';

        if($same || $has_resp || $has_vote || !$is_arg_phase)
        {
            $can_reply = 0;
        }

        return $can_reply;
    }

}
