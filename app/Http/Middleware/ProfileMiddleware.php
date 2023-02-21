<?php

namespace App\Http\Middleware;

use Closure;

class ProfileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $profile
     * @param null $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $profile, $permission = null)
    {
        if(!$request->user()->hasProfile($profile)) {
            abort(404);
        }
        if($permission !== null && !$request->user()->can($permission)) {
            abort(404);
        }
        return $next($request);
    }
}
