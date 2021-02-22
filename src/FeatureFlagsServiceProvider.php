<?php

namespace Dive\FeatureFlags;

use Dive\FeatureFlags\Commands\ListFeatureCommand;
use Dive\FeatureFlags\Commands\ToggleFeatureCommand;
use Illuminate\Support\ServiceProvider;

class FeatureFlagsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->commands([
                ListFeatureCommand::class,
                ToggleFeatureCommand::class,
            ]);
        }

        $this->registerBladeDirectives();
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
}
