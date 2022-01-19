<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Commands;

use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

class ClearCacheCommand extends Command
{
    protected $description = 'Clears the feature flags cache.';

    protected $signature = 'feature:clear';

    public function handle(CacheManager $cache, Repository $config)
    {
        $cache->store()->forget($config->get('feature-flags.cache_key'));

        $this->info('🔥  Feature flags cache cleared.');

        return self::SUCCESS;
    }
}
