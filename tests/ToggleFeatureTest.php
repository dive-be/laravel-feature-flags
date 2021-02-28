<?php

namespace Tests;

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

it('displays exception message properly if feature could not be found', function () {
    artisan('feature:toggle gibberish')
        ->assertExitCode(1)
        ->expectsOutput('🕵  The requested feature *:gibberish could not be found.');
});
