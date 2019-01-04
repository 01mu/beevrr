<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;
use Closure;
use User;
use beevrr\Http\Controllers\Common;

class CheckUserExists
{
    public function handle($request, Closure $next)
    {
        $select = \beevrr\User::select_from($request->id);

        if(count($select) === 0)
        {
            return Common::notice_msg('Invalid ID!');
        }

        return $next($request);
    }
}
