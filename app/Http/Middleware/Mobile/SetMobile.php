<?php

namespace beevrr\Http\Middleware\Mobile;

use Closure;

class SetMobile
{
    public function handle($request, Closure $next)
    {
        $request['mobile'] = 1;

        return $next($request);
    }
}
