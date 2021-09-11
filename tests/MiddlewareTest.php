<?php

namespace Tests;

use Dive\FeatureFlags\Exceptions\FeatureDisabledException;
use Dive\FeatureFlags\Middleware\EnsureFeatureEnabled;
use Dive\FeatureFlags\Models\Feature;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->mw = new EnsureFeatureEnabled(new Feature());
});

it('halts execution with 403 if feature is disabled', function () {
    $feature = Feature::factory()->isDisabled()->create();

    $this->mw->handle(new Request(), fn () => null, $feature->name);
})->throws(FeatureDisabledException::class);

it('continues the chain if feature is enabled', function () {
    $feature = Feature::factory()->create();

    $result = $this->mw->handle(new Request(), fn () => 'next', $feature->name);

    expect($result)->toBe('next');
});
