<?php

namespace Tests;

use function Pest\Laravel\artisan;
use Tests\Factories\FeatureFactory;

test('message is displayed if no features can be found', function () {
    artisan('feature:list')
        ->assertExitCode(1)
        ->expectsOutput("The application doesn't have any registered features.");
});

test('message is displayed if filtering yields no results', function () {
    FeatureFactory::new()->create();

    artisan('feature:list --scope=void')
        ->assertExitCode(1)
        ->expectsOutput("The application doesn't have any features matching the given criteria.");
});
