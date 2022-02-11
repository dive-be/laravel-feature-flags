<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Events;

use Dive\FeatureFlags\Contracts\Feature;

class FeatureToggled
{
    public function __construct(
        public readonly Feature $feature,
    ) {}

    public static function make(Feature $feature): self
    {
        return new self($feature);
    }
}
