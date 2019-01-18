<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Http\Controllers\Common;

use Closure;
use User;

class CheckUserExists
{
    public function handle($request, Closure $next)
    {
        $select = \beevrr\User::select_from($request->id);

        if(count($select) === 0)
        {
            if($request['mobile'])
            {
                return response()->json(['status' => 'failure'], 200);
            }
            else
            {
                return Common::notice_msg('Invalid ID!');
            }
        }

        return $next($request);
    }
}
