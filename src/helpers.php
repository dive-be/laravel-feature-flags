<?php

use Dive\FeatureFlags\Contracts\Feature;

if (! function_exists('feature')) {
    function feature(?string $name = null, ?string $scope = null): Feature
    {
        if (is_null($name)) {
            return app(__FUNCTION__);
        }

        return app(__FUNCTION__)->find($name, $scope);
    }
}

if (! function_exists('feature_disabled')) {
    function feature_disabled(string $name, ?string $scope = null): bool
    {
        return app('feature')->disabled($name, $scope);
    }
}

if (! function_exists('feature_enabled')) {
    function feature_enabled(string $name, ?string $scope = null): bool
    {
        return app('feature')->enabled($name, $scope);
    }
}

if (! function_exists('feature_verify')) {
    function feature_verify(string $name, ?string $scope = null)
    {
        app('feature')->verify($name, $scope);
    }
}
