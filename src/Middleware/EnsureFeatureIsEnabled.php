<?php

namespace Dive\FeatureFlags\Middleware;

use Closure;
use Dive\FeatureFlags\Feature;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureFeatureIsEnabled
{
    public function handle(Request $request, Closure $next, string $name, ?string $scope = null)
    {
        if (Feature::disabled($name, $scope)) {
            throw new AccessDeniedHttpException(Feature::find($name, $scope)->message);
        }

        return $next($request);
    }
}
