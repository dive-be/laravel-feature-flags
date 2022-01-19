<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Actions;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Config\Repository;

class ClearCacheAction
{
    public function __construct(
        private CacheManager $cache,
        private Repository $config,
    ) {}

    public function execute()
    {
        $this->cache->store()->forget(
            $this->config->get('feature-flags.cache_key')
        );
    }
}
