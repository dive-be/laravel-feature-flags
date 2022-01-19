<?php declare(strict_types=1);

namespace Dive\FeatureFlags;

use Closure;
use Dive\FeatureFlags\Commands\ClearCacheCommand;
use Dive\FeatureFlags\Commands\InstallPackageCommand;
use Dive\FeatureFlags\Commands\ListFeatureCommand;
use Dive\FeatureFlags\Commands\ToggleFeatureCommand;
use Dive\FeatureFlags\Contracts\Feature;
use Dive\FeatureFlags\Events\FeatureToggled;
use Dive\FeatureFlags\Listeners\ClearCacheListener;
use Dive\FeatureFlags\Middleware\EnsureFeatureEnabled;
use Dive\FeatureFlags\Models\Feature as Model;
use Dive\FeatureFlags\Models\Observers\FeatureObserver;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class FeatureFlagsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerConfig();
            $this->registerMigration();
        }

        $this->registerModelObservers();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/feature-flags.php', 'feature-flags');

        $this->app->alias(Feature::class, 'feature');
        $this->app->singleton(Feature::class, static function (Application $app) {
            return new ($app->make('config')->get('feature-flags.feature_model'))();
        });

        $this->callAfterResolving(Gate::class, Closure::fromCallable([$this, 'registerAtGate']));
        $this->callAfterResolving('blade.compiler', Closure::fromCallable([$this, 'registerDirectives']));
        $this->callAfterResolving('events', Closure::fromCallable([$this, 'registerListeners']));
        $this->callAfterResolving('router', Closure::fromCallable([$this, 'registerMiddleware']));
    }

    private function registerAtGate(Gate $gate)
    {
        $gate->define('feature', function (?Authenticatable $user, string $name, ?string $scope = null) {
            $feature = $this->app->make(Feature::class);

            if ($feature->disabled($name, $scope)) {
                return Response::deny($feature->find($name, $scope)->getMessage());
            }

            return Response::allow();
        });
    }

    private function registerCommands()
    {
        $this->commands([
            ClearCacheCommand::class,
            InstallPackageCommand::class,
            ListFeatureCommand::class,
            ToggleFeatureCommand::class,
        ]);
    }

    private function registerConfig()
    {
        $config = 'feature-flags.php';

        $this->publishes([
            __DIR__ . '/../config/' . $config => $this->app->configPath($config),
        ], 'config');
    }

    private function registerDirectives(BladeCompiler $blade)
    {
        $blade->directive('disabled', fn ($expression) => empty($expression)
            ? '<?php else: ?>'
            : "<?php if (feature_disabled({$expression})) : "
                . PHP_EOL . 'if (isset($message)) { $__messageOriginal = $message; } '
                . PHP_EOL . '$message = feature(' . $expression . ')->message; ?>');

        $blade->directive('enabled', fn ($expression) => empty($expression)
            ? '<?php else: ?>'
            : "<?php if (feature_enabled({$expression})) : ?>");

        $blade->directive('enddisabled', fn () => '<?php unset($message);'
            . PHP_EOL . 'if (isset($__messageOriginal)) { $message = $__messageOriginal; } '
            . PHP_EOL . 'endif ?>');

        $blade->directive('endenabled', fn () => '<?php endif ?>');
    }

    private function registerListeners(Dispatcher $dispatcher)
    {
        $dispatcher->listen(FeatureToggled::class, ClearCacheListener::class);
    }

    private function registerMiddleware(Router $router)
    {
        $router->aliasMiddleware('feature', EnsureFeatureEnabled::class);
    }

    private function registerMigration()
    {
        $migration = 'create_features_table.php';
        $doesntExist = Collection::make(glob($this->app->databasePath('migrations/*.php')))
            ->every(fn ($filename) => ! str_ends_with($filename, $migration));

        if ($doesntExist) {
            $timestamp = date('Y_m_d_His', time());
            $stub = __DIR__ . "/../database/migrations/{$migration}.stub";

            $this->publishes([
                $stub => $this->app->databasePath("migrations/{$timestamp}_{$migration}"),
            ], 'migrations');
        }
    }

    private function registerModelObservers()
    {
        Model::observe(FeatureObserver::class);
    }
}
