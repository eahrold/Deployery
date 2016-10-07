<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Traits\MiddlewareResponder;
use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    use MiddlewareResponder;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            return $this->redirectToLogin("Unauthorized", 401);
        }
        return $next($request);
    }
}
