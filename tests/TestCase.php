<?php declare(strict_types=1);

namespace Tests;

use Dive\FeatureFlags\FeatureFlagsServiceProvider;
use Dive\FeatureFlags\Models\Feature;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Feature' => Feature::class,
        ];
    }

    protected function getPackageProviders($app): array
    {
        return [FeatureFlagsServiceProvider::class];
    }

    protected function setUpDatabase($app)
    {
        $app->make('db')->connection()->getSchemaBuilder()->dropAllTables();

        $features = require __DIR__ . '/../database/migrations/create_features_table.php.stub';

        $features->up();
    }
}
