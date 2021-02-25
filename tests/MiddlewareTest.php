<?php

namespace Tests;

use Dive\FeatureFlags\Middleware\EnsureFeatureIsEnabled;
use Dive\FeatureFlags\Models\Feature;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

beforeEach(function () {
    $this->mw = new EnsureFeatureIsEnabled(new Feature());
});

it('halts execution with 403 if feature is disabled', function () {
    $feature = Feature::factory()->isDisabled()->create();

    $this->mw->handle(new Request(), fn () => null, $feature->name);
})->throws(AccessDeniedHttpException::class);

it('continues the chain if feature is enabled', function () {
    $feature = Feature::factory()->create();

    $result = $this->mw->handle(new Request(), fn () => 'next', $feature->name);

    expect($result)->toBe('next');
});
