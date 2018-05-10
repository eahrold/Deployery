<?php

namespace App\Http\Middleware;

use Closure;

class CORS {

    public function handle($request, Closure $next)
    {
        if ( ! $this->isCorsRequest($request)) {
            return $next($request);
        }

        if($this->isPreflightRequest($request)) {
            return $this->handlePreflightRequest($request);
        }

        $response = $next($request);
        return $this->addOriginToResponse($request, $response);

    }

    protected function isPreflightRequest($request)
    {
        return $request->getMethod() === 'OPTIONS';
    }

    protected function isCorsRequest($request): bool
    {
        if (! $request->headers->has('Origin')) {
            return false;
        }
        return $request->headers->get('Origin') !== $request->getSchemeAndHttpHost();
    }

    protected function isExplicitlyAllowedOrigin($origin, $allowed)
    {
        $host = str_replace(['http://', 'https://'], '', $origin);
        foreach ($allowed as $match) {
            if(str_is($match, $host)) {
                return true;
            }
        }
        return false;
    }

    protected function addOriginToResponse($request, $response)
    {
        $origin = $request->headers->get('Origin');
        $schema = $request->getSchemeAndHttpHost();
        $allowed = config('cors.allowed_origin', ['*']);

        if(($origin !== $schema) && $this->isExplicitlyAllowedOrigin($origin, $allowed)) {
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Allow-Origin', $origin);
        } else {
            $response->header('Access-Control-Allow-Origin', '*');
        }
        return $response;
    }

    protected function handlePreflightRequest($request)
    {
        $response = response('Preflight OK', 200)
            ->header('Access-Control-Allow-Headers', implode(',', config('cors.headers', [])))
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        return $this->addOriginToResponse($request, $response);
    }
}