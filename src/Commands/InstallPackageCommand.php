<?php

namespace Dive\FeatureFlags\Commands;

use Illuminate\Console\Command;

class InstallPackageCommand extends Command
{
    protected $description = 'Install feature flags.';

    protected $signature = 'feature:install';

    public function handle()
    {
        $this->info('🏎  Installing feature flags...');
        $this->info('📑  Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Dive\FeatureFlags\FeatureFlagsServiceProvider",
            '--tag' => 'config',
        ]);

        $this->info('📑  Publishing migration...');

        $this->call('vendor:publish', [
            '--provider' => "Dive\FeatureFlags\FeatureFlagsServiceProvider",
            '--tag' => 'migrations',
        ]);

        $this->info('🏁  Feature flags installed successfully!');
    }

    public function isHidden()
    {
        return file_exists(config_path('feature-flags.php'));
    }
}
