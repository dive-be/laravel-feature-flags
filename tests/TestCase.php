<?php declare(strict_types=1);

namespace Tests;

use CreateFeaturesTable;
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

    protected function getPackageAliases($app)
    {
        return [
            'Feature' => Feature::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [FeatureFlagsServiceProvider::class];
    }

    protected function setUpDatabase($app)
    {
        $app->make('db')->connection()->getSchemaBuilder()->dropAllTables();

        require_once __DIR__ . '/../database/migrations/create_features_table.php.stub';

        (new CreateFeaturesTable())->up();
    }
}
