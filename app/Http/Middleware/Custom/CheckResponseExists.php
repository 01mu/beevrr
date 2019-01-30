<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Http\Controllers\Common;

use beevrr\Models\ResponseModel;

use Closure;
use Auth;

class CheckResponseExists
{
    public function handle($request, Closure $next)
    {
        if(!ResponseModel::select_from($request->id))
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
