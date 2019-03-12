<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\IpUtils;

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
        $check = (bool)config('webhooks.should_validate');
        if ($check && !$this->validWebhook($request)) {
            return response()->json([
                "message" => "{$request->ip()} is not a valid webhook source."
            ], 403);
        }
        return $next($request);
    }

    /**
     * Check if the source of the webhook is whitelisted
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool    true if the webhook is valid, false otherwise.
     */
    private function validWebhook($request)
    {
        $ip = $request->ip();
        $userAgent = Str::slug($request->header('User-Agent'));

        $sources = config('webhooks.sources');
        foreach ($sources as $source) {
            $prefix = $source['user_agent_prefix'];
            foreach ($source['whitelist'] as $range) {
                if ($ip === $range || IpUtils::checkIp($ip, $range)) {
                    return $prefix === '*' || starts_with($userAgent, Str::slug($prefix));
                }
            }
        }
        return false;
    }
}
