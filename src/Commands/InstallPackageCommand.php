<?php

namespace Dive\FeatureFlags\Commands;

use Illuminate\Console\Command;

class InstallPackageCommand extends Command
{
    protected $description = 'Install feature flags.';

    protected $signature = 'feature:install';

    public function handle()
    {
        if ($this->isHidden()) {
            $this->error('🤚  Feature flags is already installed.');

            return 1;
        }

        $this->line('🏎  Installing feature flags...');
        $this->line('📑  Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Dive\FeatureFlags\FeatureFlagsServiceProvider",
            '--tag' => 'config',
        ]);

        $this->line('📑  Publishing migration...');

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
