<?php

namespace Dive\FeatureFlags\Commands;

use Dive\FeatureFlags\Models\Feature;
use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;

class ClearCacheCommand extends Command
{
    protected $description = 'Clears the feature flags cache.';

    protected $signature = 'feature:clear';

    public function handle(CacheManager $cache)
    {
        $cache->store()->forget(Feature::CACHE);

        $this->info('🔥  Feature flags cache cleared.');

        return self::SUCCESS;
    }
}
