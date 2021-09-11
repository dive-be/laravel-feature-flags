<?php

namespace Dive\FeatureFlags\Middleware;

use Closure;
use Dive\FeatureFlags\Contracts\Feature;
use Dive\FeatureFlags\Exceptions\FeatureDisabledException;
use Illuminate\Http\Request;

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
            throw FeatureDisabledException::make($this->feature->find($name, $scope));
        }

        return $next($request);
    }
}
