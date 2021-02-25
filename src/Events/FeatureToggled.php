<?php

namespace Dive\FeatureFlags\Events;

use Dive\FeatureFlags\Contracts\Feature;

class FeatureToggled
{
    public function __construct(public Feature $feature)
    {
    }

    public static function make(Feature $feature)
    {
        return new self($feature);
    }
}
