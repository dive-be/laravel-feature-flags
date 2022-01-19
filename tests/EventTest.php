<?php declare(strict_types=1);

namespace Tests;

use Dive\FeatureFlags\Events\FeatureToggled;
use Dive\FeatureFlags\Models\Feature;

it('is makeable', function () {
    expect(FeatureToggled::make(new Feature()))->toBeInstanceOf(FeatureToggled::class);
});

it('provides the toggled feature', function () {
    $event = new FeatureToggled($feature = new Feature());

    expect($event->feature)->toBe($feature);
});
