<?php

namespace Tests;

use Dive\FeatureFlags\Models\Feature;
use function Pest\Laravel\artisan;

test('message is displayed if no features can be found', function () {
    artisan('feature:list')
        ->assertExitCode(1)
        ->expectsOutput("The application doesn't have any registered features.");
});

test('message is displayed if filtering yields no results', function () {
    Feature::factory()->create();

    artisan('feature:list --scope=void')
        ->assertExitCode(1)
        ->expectsOutput("The application doesn't have any features matching the given criteria.");
});
