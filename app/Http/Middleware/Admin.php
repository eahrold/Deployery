<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Traits\MiddlewareResponder;
use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
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
        $user = Auth::user();
        $route_user = $request->route('user');
        if ($user && ($user->is_admin || $user->id == $route_user)) {
            return $next($request);
        } else {
            return $this->redirectToDefault();
        }
    }
}
