<?php

namespace Dive\FeatureFlags\Middleware;

use Closure;
use Dive\FeatureFlags\Contracts\Feature;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureFeatureEnabled
{
    public function __construct(private Feature $feature) {}

    public function handle(
        Request $request,
        Closure $next,
        string $name,
        ?string $scope = null,
    ) {
        if ($this->feature->disabled($name, $scope)) {
            throw new AccessDeniedHttpException($this->feature->find($name, $scope)->getMessage());
        }

        return $next($request);
    }
}
