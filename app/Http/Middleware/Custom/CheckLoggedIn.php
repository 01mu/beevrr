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
            if($request['mobile'])
            {
                return response()->json(['status' => 'not_logged_in'], 200);
            }
            else
            {
                return Common::notice_msg('Not logged in!');
            }
        }

        return $next($request);
    }
}
