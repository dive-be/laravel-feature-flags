<?php

namespace Dive\FeatureFlags\Middleware;

use Closure;
use Dive\FeatureFlags\Feature;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureFeatureIsEnabled
{
    public function handle(Request $request, Closure $next, ...$args)
    {
        if (Feature::disabled(...$args)) {
            throw new AccessDeniedHttpException(Feature::find(...$args)->message);
        }

        return $next($request);
    }
}
