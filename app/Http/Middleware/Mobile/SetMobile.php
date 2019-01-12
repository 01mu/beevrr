<?php

namespace beevrr\Http\Middleware\Mobile;

use Closure;

class SetMobile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request['mobile'] = 1;
        return $next($request);
    }
}
