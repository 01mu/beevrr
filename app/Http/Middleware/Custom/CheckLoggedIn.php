<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;
use beevrr\Http\Controllers\Common;
use Closure;
use Auth;

class CheckLoggedIn
{
    public function handle($request, Closure $next)
    {
        if(!Auth::check())
        {
            return Common::notice_msg('Not logged in!');
        }

        return $next($request);
    }
}
