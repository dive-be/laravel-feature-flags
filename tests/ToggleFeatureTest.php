<?php

namespace Tests;

use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use Dive\FeatureFlags\Models\Feature;
use function Pest\Laravel\artisan;

it('asks for confirmation in production', function () {
    app()->env = 'production';
    $feature = Feature::factory()->withName($name = 'dive')->create();

    artisan("feature:toggle {$name}")
        ->expectsConfirmation("🤔  Are you sure you'd like to continue? [currently {$feature->getState()}]");
});

it('can toggle the feature states', function () {
    $feature = Feature::factory()->withName($name = 'dive')->create();

    expect($feature->isEnabled())->toBeTrue();

    artisan("feature:toggle {$name}")->execute();

    expect($feature->refresh()->isEnabled())->toBeFalse();
});

it('throws if a feature cannot be found', function () {
    artisan('feature:toggle gibberish')->execute();
})->throws(UnknownFeatureException::class);
