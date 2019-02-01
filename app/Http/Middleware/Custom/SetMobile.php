<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Middleware\Custom;

use Closure;

class SetMobile
{
    public function handle($request, Closure $next)
    {
        $request['mobile'] = 1;

        return $next($request);
    }
}
