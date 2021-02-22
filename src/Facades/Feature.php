<?php

namespace Dive\FeatureFlags\Facades;

use Dive\FeatureFlags\Contracts\Feature as Contract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool disabled(string $name, string|null $scope = null)
 * @method static bool enabled(string $name, string|null $scope = null)
 * @method static \Dive\FeatureFlags\Models\Feature find(string $name, string|null $scope = null)
 */
class Feature extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Contract::class;
    }
}
