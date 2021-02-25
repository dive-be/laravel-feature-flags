<?php

namespace Tests;

use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use function Pest\Laravel\artisan;
use Tests\Factories\FeatureFactory;

it('asks for confirmation in production', function () {
    app()->env = 'production';
    FeatureFactory::new()->withName($name = 'dive')->create();

    artisan("feature:toggle {$name}")->expectsConfirmation("🤔  Are you sure you'd like to continue?");
});

it('can toggle the feature states', function () {
    $feature = FeatureFactory::new()->withName($name = 'dive')->create();

    expect($feature->isEnabled())->toBeTrue();

    artisan("feature:toggle {$name}")->execute();

    expect($feature->refresh()->isEnabled())->toBeFalse();
});

it('throws if a feature cannot be found', function () {
    artisan('feature:toggle gibberish')->execute();
})->throws(UnknownFeatureException::class);
