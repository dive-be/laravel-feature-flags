<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Middleware;

use Closure;
use Dive\FeatureFlags\Contracts\Feature;
use Illuminate\Http\Request;

class EnsureFeatureEnabled
{
    public function __construct(
        private Feature $feature,
    ) {}

    public function handle(Request $request, Closure $next, string $name, ?string $scope = null)
    {
        $this->feature->verify($name, $scope);

        return $next($request);
    }
}
