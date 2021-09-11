<?php

namespace Dive\FeatureFlags\Models\Observers;

use Dive\FeatureFlags\Models\Feature;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class FeatureObserver
{
    public function creating(Feature $feature)
    {
        $feature->scope ??= Feature::GENERAL;
    }

    public function saved()
    {
        Cache::forget(Config::get('feature-flags.cache_key'));
    }
}
