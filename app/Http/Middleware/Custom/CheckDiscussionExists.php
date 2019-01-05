<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Models\DiscussionModel;
use beevrr\Http\Controllers\Common;

use Closure;

class CheckDiscussionExists
{
    public function handle($request, Closure $next)
    {
        $exists = count(DiscussionModel::select_from($request->id));

        if($exists === 0)
        {
            return Common::notice_msg('Invalid ID!');
        }

        return $next($request);
    }
}
