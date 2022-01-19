<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Commands;

use Dive\FeatureFlags\Actions\ClearCacheAction;
use Illuminate\Console\Command;

class ClearCacheCommand extends Command
{
    protected $description = 'Clears the feature flags cache.';

    protected $signature = 'feature:clear';

    public function handle(ClearCacheAction $action)
    {
        $action->execute();

        $this->info('🔥  Feature flags cache cleared.');

        return self::SUCCESS;
    }
}
