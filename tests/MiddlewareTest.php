<?php

namespace Tests;

use Dive\FeatureFlags\Feature;
use Dive\FeatureFlags\Middleware\EnsureFeatureIsEnabled;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\Factories\FeatureFactory;

beforeEach(function () {
    $this->mw = new EnsureFeatureIsEnabled(new Feature());
});

it('halts execution with 403 if feature is disabled', function () {
    $feature = FeatureFactory::new()->isDisabled()->create();

    $this->mw->handle(new Request(), fn () => null, $feature->name);
})->throws(AccessDeniedHttpException::class);

it('continues the chain if feature is enabled', function () {
    $feature = FeatureFactory::new()->create();

    $result = $this->mw->handle(new Request(), fn () => 'next', $feature->name);

    expect($result)->toBe('next');
});
