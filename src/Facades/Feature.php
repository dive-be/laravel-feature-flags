<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool disabled(string $name, string|null $scope = null)
 * @method static bool enabled(string $name, string|null $scope = null)
 * @method static \Dive\FeatureFlags\Models\Feature find(string $name, string|null $scope = null)
 * @method static void verify(string $name, string|null $scope = null)
 */
class Feature extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'feature';
    }
}
