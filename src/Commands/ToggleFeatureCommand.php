<?php

namespace Dive\FeatureFlags\Commands;

use Dive\FeatureFlags\Contracts\Feature;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

class ToggleFeatureCommand extends Command
{
    protected $description = 'Toggle a specific feature on/off.';

    protected $signature = 'feature:toggle {name} {scope?}';

    public function handle(Application $app, Feature $feature)
    {
        $found = $feature->find($this->argument('name'), $this->argument('scope'));

        if (! $app->isProduction() || $this->confirm("🤔  Are you sure you'd like to continue?")) {
            $found->toggle();

            $this->line("👉  The feature <fg=yellow>{$found->getLabel()}</> is now {$found->getState()}");
        }
    }
}
