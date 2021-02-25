<?php

namespace Tests;

use Dive\FeatureFlags\Contracts\Feature as Contract;
use Dive\FeatureFlags\Models\Feature;

test('feature finds and returns a specific feature', function () {
    $featureA = Feature::factory()->create();

    $featureB = feature($featureA->name, $featureA->scope);

    expect($featureA->is($featureB))->toBeTrue();
});

test('feature returns an instance if no parameters are passed', function () {
    expect(feature())->toBe(app(Contract::class));
});

test('feature_disabled determines whether a feature has been disabled', function () {
    $featureA = Feature::factory()->create();
    $featureB = Feature::factory()->isDisabled()->create();

    expect(feature_disabled($featureA->name, $featureA->scope))->toBeFalse();
    expect(feature_disabled($featureB->name, $featureB->scope))->toBeTrue();
});

test('feature_enabled determines whether a feature has been enabled', function () {
    $featureA = Feature::factory()->create();
    $featureB = Feature::factory()->isDisabled()->create();

    expect(feature_enabled($featureA->name, $featureA->scope))->toBeTrue();
    expect(feature_enabled($featureB->name, $featureB->scope))->toBeFalse();
});
