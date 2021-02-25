<?php

namespace Dive\FeatureFlags;

use Dive\FeatureFlags\Commands\InstallPackageCommand;
use Dive\FeatureFlags\Commands\ListFeatureCommand;
use Dive\FeatureFlags\Commands\ToggleFeatureCommand;
use Dive\FeatureFlags\Contracts\Feature;
use Dive\FeatureFlags\Middleware\EnsureFeatureIsEnabled;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FeatureFlagsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishables();

            $this->commands([
                InstallPackageCommand::class,
                ListFeatureCommand::class,
                ToggleFeatureCommand::class,
            ]);
        }

        $this->registerBladeDirectives();

        $this->app->make('router')->aliasMiddleware('feature', EnsureFeatureIsEnabled::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/feature-flags.php', 'feature-flags');

        $this->app->singleton(Feature::class, static function (Application $app) {
            return new ($app->make('config')->get('feature-flags.feature_model'))();
        });
    }

    private function registerBladeDirectives()
    {
        $this->app['blade.compiler']->directive('disabled', fn ($expression) => "<?php if (feature_disabled({$expression})) : "
            .PHP_EOL.'if (isset($message)) { $__messageOriginal = $message; } '
            .PHP_EOL.'$message = feature('.$expression.')->message; ?>');

        $this->app['blade.compiler']->directive('enabled', fn () => '<?php else: ?>');

        $this->app['blade.compiler']->directive('enddisabled', fn () => '<?php unset($message);'
            .PHP_EOL.'if (isset($__messageOriginal)) { $message = $__messageOriginal; } '
            .PHP_EOL.'endif ?>');
    }

    private function registerPublishables()
    {
        if (! class_exists('CreateFeaturesTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_features_table.php.stub' => $this->app->databasePath('migrations/'.date('Y_m_d_His', time()).'_create_features_table.php'),
            ], 'migrations');
        }

        $this->publishes([
            __DIR__.'/../config/feature-flags.php' => $this->app->configPath('feature-flags.php'),
        ], 'config');
    }
}
