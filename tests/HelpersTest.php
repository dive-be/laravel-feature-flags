<?php

namespace Tests;

use Dive\FeatureFlags\Contracts\Feature;
use Tests\Factories\FeatureFactory;

test('feature finds and returns a specific feature', function () {
    $featureA = FeatureFactory::new()->create();

    $featureB = feature($featureA->name, $featureA->scope);

    expect($featureA->is($featureB))->toBeTrue();
});

test('feature returns an instance if no parameters are passed', function () {
    expect(feature())->toBe(app(Feature::class));
});

test('feature_disabled determines whether a feature has been disabled', function () {
    $featureA = FeatureFactory::new()->create();
    $featureB = FeatureFactory::new()->isDisabled()->create();

    expect(feature_disabled($featureA->name, $featureA->scope))->toBeFalse();
    expect(feature_disabled($featureB->name, $featureB->scope))->toBeTrue();
});

test('feature_enabled determines whether a feature has been enabled', function () {
    $featureA = FeatureFactory::new()->create();
    $featureB = FeatureFactory::new()->isDisabled()->create();

    expect(feature_enabled($featureA->name, $featureA->scope))->toBeTrue();
    expect(feature_enabled($featureB->name, $featureB->scope))->toBeFalse();
});
