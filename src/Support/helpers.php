<?php

use Dive\FeatureFlags\Contracts\Feature as Contract;
use Dive\FeatureFlags\Models\Feature;

if (! function_exists('feature')) {
    function feature(?string $name = null, ?string $scope = null): Feature
    {
        if (is_null($name)) {
            return app(Contract::class);
        }

        return app(Contract::class)->find($name, $scope);
    }
}

if (! function_exists('feature_disabled')) {
    function feature_disabled(string $name, ?string $scope = null): bool
    {
        return app(Contract::class)->disabled($name, $scope);
    }
}

if (! function_exists('feature_enabled')) {
    function feature_enabled(string $name, ?string $scope = null): bool
    {
        return app(Contract::class)->enabled($name, $scope);
    }
}
