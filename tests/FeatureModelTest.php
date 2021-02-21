<?php

namespace Tests;

use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use Dive\FeatureFlags\Feature;
use Tests\Factories\FeatureFactory;

it('allows identical feature names using distinct scopes', function () {
    $factory = FeatureFactory::new()->withName($name = 'onboarding');
    $factory->withScope($scopeA = 'admin')->create();
    $factory->withScope($scopeB = 'member')->create();

    $featureA = Feature::find($name, $scopeA);
    $featureB = Feature::find($name, $scopeB);

    expect($featureA->is($featureB))->toBeFalse();
    expect($featureA->unique_name)->not->toBe($featureB->unique_name);
});

it('can determine whether a feature has been disabled', function () {
    $featureA = FeatureFactory::new()->create();
    $featureB = FeatureFactory::new()->isDisabled()->create();

    expect(Feature::disabled($featureA->name, $featureA->scope))->toBeFalse();
    expect(Feature::disabled($featureB->name, $featureB->scope))->toBeTrue();
});

it('can determine whether a feature has been enabled', function () {
    $featureA = FeatureFactory::new()->create();
    $featureB = FeatureFactory::new()->isDisabled()->create();

    expect(Feature::enabled($featureA->name, $featureA->scope))->toBeTrue();
    expect(Feature::enabled($featureB->name, $featureB->scope))->toBeFalse();
});

it('can find an existing feature', function () {
    FeatureFactory::new()->withName($name = 'checkout')->create();

    $feature = Feature::find($name);

    expect($feature->name)->toBe($name);
});

it('can toggle the state of a feature', function () {
    $feature = FeatureFactory::new()->create();

    expect($feature->is_enabled)->toBeTrue();

    $feature->toggle();

    expect($feature->is_enabled)->toBeFalse();
});

it('has an is_enabled accessor', function () {
    $featureA = FeatureFactory::new()->make();
    $featureB = FeatureFactory::new()->isDisabled()->make();

    expect($featureA->is_enabled)->toBeTrue();
    expect($featureB->is_enabled)->toBeFalse();
});

it('has a unique_name accessor', function () {
    $feature = FeatureFactory::new()->withScope($scope = 'webshop')->make();

    expect($feature->unique_name)->toBe($scope.'.'.$feature->name);
});

it('is stringable', function () {
    $feature = FeatureFactory::new()->withLabel($label = 'Gift certificates')->create();

    expect($feature->__toString())->toBe($label);
});

it('sets scope to "*" before creating', function () {
    $feature = FeatureFactory::new()->make();

    expect($feature->scope)->toBeNull();

    $feature->save();

    expect($feature->scope)->toBe('*');
});

it('throws if a feature cannot be found', function () {
    Feature::find('gibberish');
})->throws(UnknownFeatureException::class);
