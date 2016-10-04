<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

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
        if (($token = $request->header('X-Authorization')) &&
           ($user = User::where('remember_token', $token)->first())) {
                Auth::login($user);
                return $next($request);
        }

        // This looks backwards, but a failed auth attemt will
        // return a Response object, and successful one will return null
        if ($response = $this->auth->basic()) {
            return $response;
        }
        $next($request);
    }
}
