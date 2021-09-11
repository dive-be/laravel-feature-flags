<?php

namespace Tests;

use Dive\FeatureFlags\Contracts\Feature as Contract;
use Dive\FeatureFlags\Events\FeatureToggled;
use Dive\FeatureFlags\Exceptions\FeatureDisabledException;
use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use Dive\FeatureFlags\Models\Feature;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->model = new Feature();
});

it('adheres to the contract', function () {
    expect($this->model)->toBeInstanceOf(Contract::class);
});

it('allows identical feature names using distinct scopes', function () {
    $factory = Feature::factory()->withName($name = 'onboarding');
    $factory->withScope($scopeA = 'admin')->create();
    $factory->withScope($scopeB = 'member')->create();

    $featureA = $this->model->find($name, $scopeA);
    $featureB = $this->model->find($name, $scopeB);

    expect($featureA->is($featureB))->toBeFalse();
    expect($featureA->unique_name)->not->toBe($featureB->unique_name);
});

it('can determine whether a feature is enabled', function () {
    $featureA = Feature::factory()->make();
    $featureB = Feature::factory()->isDisabled()->make();

    expect($featureA->isEnabled())->toBeTrue();
    expect($featureB->isEnabled())->toBeFalse();
});

it('can determine whether a feature is disabled', function () {
    $featureA = Feature::factory()->make();
    $featureB = Feature::factory()->isDisabled()->make();

    expect($featureA->isDisabled())->toBeFalse();
    expect($featureB->isDisabled())->toBeTrue();
});

it('can find and determine whether a feature has been disabled', function () {
    $featureA = Feature::factory()->create();
    $featureB = Feature::factory()->isDisabled()->create();

    expect($this->model->disabled($featureA->name, $featureA->scope))->toBeFalse();
    expect($this->model->disabled($featureB->name, $featureB->scope))->toBeTrue();
});

it('can find and determine whether a feature has been enabled', function () {
    $featureA = Feature::factory()->create();
    $featureB = Feature::factory()->isDisabled()->create();

    expect($this->model->enabled($featureA->name, $featureA->scope))->toBeTrue();
    expect($this->model->enabled($featureB->name, $featureB->scope))->toBeFalse();
});

it('can find an existing feature', function () {
    Feature::factory()->withName($name = 'checkout')->create();

    $feature = $this->model->find($name);

    expect($feature->name)->toBe($name);
});

it('can get all features', function () {
    $features = Feature::factory(5)->create();

    expect($this->model->getFeatures()->pluck('id'))->toEqualCanonicalizing($features->pluck('id'));
});

it('can get the description', function () {
    $feature = Feature::factory()
        ->withDescription($description = 'Blocks access to admin portal when disabled')
        ->create();

    expect($feature->getDescription())->toBe($description);
});

it('can get the label', function () {
    $feature = Feature::factory()
        ->withLabel($label = 'Admin portal')
        ->create();

    expect($feature->getLabel())->toBe($label);
});

it('can get the message', function () {
    $feature = Feature::factory()
        ->withMessage($message = 'Due to high traffic, we are temporarily unavailable.')
        ->create();

    expect($feature->getMessage())->toBe($message);
});

it('can toggle the state of a feature', function () {
    $feature = Feature::factory()->create();

    expect($feature->isEnabled())->toBeTrue();

    $feature->toggle();

    expect($feature->isDisabled())->toBeTrue();
});

it('fires an event when toggled', function () {
    $feature = Feature::factory()->create();

    Event::fake();

    $feature->toggle();

    Event::assertDispatched(FeatureToggled::class);
});

it('has a state accessor and getter', function () {
    $featureA = Feature::factory()->make();
    $featureB = Feature::factory()->isDisabled()->make();

    expect($featureA->state)->toBe('<fg=green>enabled</>');
    expect($featureB->state)->toBe('<fg=red>disabled</>');
    expect($featureA->getState())->toBe('<fg=green>enabled</>');
    expect($featureB->getState())->toBe('<fg=red>disabled</>');
});

it('has a unique_name accessor', function () {
    $feature = Feature::factory()->withScope($scope = 'webshop')->make();

    expect($feature->unique_name)->toBe($scope.'.'.$feature->name);
});

it('is stringable', function () {
    $feature = Feature::factory()->withLabel($label = 'Gift certificates')->create();

    expect($feature->__toString())->toBe($label);
});

it('sets scope to "*" before creating', function () {
    $feature = Feature::factory()->make();

    expect($feature->scope)->toBeNull();

    $feature->save();

    expect($feature->scope)->toBe('*');
});

it('throws if a feature cannot be found', function () {
    $this->model->find('gibberish');
})->throws(UnknownFeatureException::class);

it('can verify a given feature', function () {
    $feature = Feature::factory()->withName('dashboard')->create();

    expect(fn () => $feature->verify('dashboard'))->not->toThrow(FeatureDisabledException::class);

    $feature->toggle();

    expect(fn () => $feature->verify('dashboard'))->toThrow(FeatureDisabledException::class);
});
