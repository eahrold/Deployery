<?php

namespace App\Http\Middleware;

use Closure;

class ValidateWebhookSource
{

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
        // Verify the request is GH or Bb or CD, etc...
        return $next($request);
    }
}
