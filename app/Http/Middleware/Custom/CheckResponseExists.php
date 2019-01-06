<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use beevrr\Models\ResponseModel;
use beevrr\Http\Controllers\Common;

use Closure;
use Auth;

class CheckResponseExists
{
    public function handle($request, Closure $next)
    {
        if(!ResponseModel::select_from($request->id))
        {
            return Common::notice_msg('Invalid ID!');
        }

        return $next($request);
    }
}
