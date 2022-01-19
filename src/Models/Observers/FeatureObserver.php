<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Models\Observers;

use Dive\FeatureFlags\Actions\ClearCacheAction;
use Dive\FeatureFlags\Models\Feature;
use Illuminate\Support\Facades\App;

class FeatureObserver
{
    public function creating(Feature $feature)
    {
        $feature->scope ??= Feature::getDefaultScope();
    }

    public function saved()
    {
        App::make(ClearCacheAction::class)->execute();
    }
}
