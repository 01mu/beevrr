<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Http\Controllers\Common;

use beevrr\Models\DiscussionModel;

use Closure;

class CheckDiscussionExists
{
    public function handle($request, Closure $next)
    {
        if(!DiscussionModel::select_from($request->id))
        {
            if($request['mobile'])
            {
                return response()->json(['status' => 'not_logged_in'], 200);
            }
            else
            {
                return Common::notice_msg('Invalid ID!');
            }
        }

        return $next($request);
    }
}
