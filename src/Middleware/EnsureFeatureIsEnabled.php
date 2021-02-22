<?php

namespace Dive\FeatureFlags\Middleware;

use Closure;
use Dive\FeatureFlags\Models\Feature;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureFeatureIsEnabled
{
    private Feature $feature;

    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
    }

    public function handle(Request $request, Closure $next, string $name, ?string $scope = null)
    {
        if ($this->feature->disabled($name, $scope)) {
            throw new AccessDeniedHttpException($this->feature->find($name, $scope)->message);
        }

        return $next($request);
    }
}
