<?php

use Dive\FeatureFlags\Feature;

if (! function_exists('feature')) {
    function feature(string $name, ?string $scope = null): Feature
    {
        return Feature::find($name, $scope);
    }
}

if (! function_exists('feature_disabled')) {
    function feature_disabled(string $name, ?string $scope = null): bool
    {
        return Feature::disabled($name, $scope);
    }
}

if (! function_exists('feature_enabled')) {
    function feature_enabled(string $name, ?string $scope = null): bool
    {
        return Feature::enabled($name, $scope);
    }
}
