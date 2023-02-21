<?php

namespace App\Http\Middleware;

use Closure;

class FirstLoginMiddleware
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
        if($request->user()->first_login) {
            return redirect()->route('first-login-create');
        }
        return $next($request);
    }
}
