<?php

namespace Dive\FeatureFlags\Commands;

use Dive\FeatureFlags\Feature;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

class ToggleFeatureCommand extends Command
{
    protected $description = 'Toggle a specific feature on/off.';

    protected $signature = 'feature:toggle {name} {scope?}';

    public function handle(Application $app)
    {
        if (! is_string($name = $this->argument('name'))) {
            $this->error('You must provide a valid feature name.');

            return 1;
        }

        if (is_array($scope = $this->argument('scope'))) {
            $this->error('You may only provide a single scope.');

            return 1;
        }

        $feature = Feature::find($name, $scope);

        $this->printState($feature, 'ℹ️', 'currently');

        if (! $app->isProduction() || $this->confirm("🤔  Are you sure you'd like to continue?")) {
            $feature->toggle();

            $this->printState($feature, '🏁', 'now');
        }
    }

    private function printState(Feature $feature, string $emoji, string $occurrence)
    {
        $state = $feature->is_enabled ? '<bg=green>active</>' : '<bg=red>inactive</>';

        $this->info("{$emoji}  The feature {$feature} is {$occurrence} {$state}.");
    }
}
