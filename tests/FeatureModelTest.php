<?php

namespace Tests;

use Dive\FeatureFlags\Contracts\Feature as Contract;
use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use Dive\FeatureFlags\Models\Feature;
use Tests\Factories\FeatureFactory;

beforeEach(function () {
    $this->model = new Feature();
});

it('allows identical feature names using distinct scopes', function () {
    $factory = FeatureFactory::new()->withName($name = 'onboarding');
    $factory->withScope($scopeA = 'admin')->create();
    $factory->withScope($scopeB = 'member')->create();

    $featureA = $this->model->find($name, $scopeA);
    $featureB = $this->model->find($name, $scopeB);

    expect($featureA->is($featureB))->toBeFalse();
    expect($featureA->unique_name)->not->toBe($featureB->unique_name);
});

it('can determine whether a feature is enabled', function () {
    $featureA = FeatureFactory::new()->make();
    $featureB = FeatureFactory::new()->isDisabled()->make();

    expect($featureA->isEnabled())->toBeTrue();
    expect($featureB->isEnabled())->toBeFalse();
});

it('can determine whether a feature is disabled', function () {
    $featureA = FeatureFactory::new()->make();
    $featureB = FeatureFactory::new()->isDisabled()->make();

    expect($featureA->isDisabled())->toBeFalse();
    expect($featureB->isDisabled())->toBeTrue();
});

it('can find and determine whether a feature has been disabled', function () {
    $featureA = FeatureFactory::new()->create();
    $featureB = FeatureFactory::new()->isDisabled()->create();

    expect($this->model->disabled($featureA->name, $featureA->scope))->toBeFalse();
    expect($this->model->disabled($featureB->name, $featureB->scope))->toBeTrue();
});

it('can find and determine whether a feature has been enabled', function () {
    $featureA = FeatureFactory::new()->create();
    $featureB = FeatureFactory::new()->isDisabled()->create();

    expect($this->model->enabled($featureA->name, $featureA->scope))->toBeTrue();
    expect($this->model->enabled($featureB->name, $featureB->scope))->toBeFalse();
});

it('can find an existing feature', function () {
    FeatureFactory::new()->withName($name = 'checkout')->create();

    $feature = $this->model->find($name);

    expect($feature->name)->toBe($name);
});

it('can get the description', function () {
    $feature = FeatureFactory::new()
        ->withDescription($description = 'Blocks access to admin portal when disabled')
        ->create();

    expect($feature->getDescription())->toBe($description);
});

it('can get the label', function () {
    $feature = FeatureFactory::new()
        ->withLabel($label = 'Admin portal')
        ->create();

    expect($feature->getLabel())->toBe($label);
});

it('can get the message', function () {
    $feature = FeatureFactory::new()
        ->withMessage($message = 'Due to high traffic, we are temporarily unavailable.')
        ->create();

    expect($feature->getMessage())->toBe($message);
});


it('can toggle the state of a feature', function () {
    $feature = FeatureFactory::new()->create();

    expect($feature->isEnabled())->toBeTrue();

    $feature->toggle();

    expect($feature->isDisabled())->toBeTrue();
});

it('has a unique_name accessor', function () {
    $feature = FeatureFactory::new()->withScope($scope = 'webshop')->make();

    expect($feature->unique_name)->toBe($scope.'.'.$feature->name);
});

it('adheres to the contract', function () {
    expect($this->model)->toBeInstanceOf(Contract::class);
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
    $this->model->find('gibberish');
})->throws(UnknownFeatureException::class);
