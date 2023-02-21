<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->email_verified_at) {
            return $request->expectsJson()
                ? abort(403, 'Votre adrese email n\'est pas vérifée!')
                : Redirect::route("show-verification");
        }

        return $next($request);
    }
}
