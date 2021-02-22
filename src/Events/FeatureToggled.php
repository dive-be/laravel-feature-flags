<?php

namespace Dive\FeatureFlags\Events;

use Dive\FeatureFlags\Contracts\Feature;

class FeatureToggled
{
    public Feature $feature;

    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
    }

    public static function make(Feature $feature)
    {
        return new self($feature);
    }
}
