<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Mobile;

use beevrr\Http\Controllers\Common;

use Closure;
use Auth;

class CheckLoggedIn
{
    public function handle($request, Closure $next)
    {
        if(!Auth::check())
        {
            return response()->json(['status'=>'not_logged_in'], 200);
        }

        return $next($request);
    }
}
