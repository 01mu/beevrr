<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;
use Closure;
use beevrr\Models\DiscussionModel;
use beevrr\Http\Controllers\Common;

class CheckDiscussionExists
{
    public function handle($request, Closure $next)
    {
        $exists = count(DiscussionModel::check_exists($request->id));

        if($exists === 0)
        {
            return Common::notice_msg('Invalid ID!');
        }

        return $next($request);
    }
}
