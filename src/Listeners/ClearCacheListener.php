<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Listeners;

use Dive\FeatureFlags\Actions\ClearCacheAction;

class ClearCacheListener
{
    public function __construct(
        private ClearCacheAction $action,
    ) {}

    public function handle()
    {
        $this->action->execute();
    }
}
