<?php

namespace Tests;

use function Pest\Laravel\artisan;

it('clears the cache', function () {
    artisan('feature:clear')
        ->assertExitCode(0)
        ->expectsOutput('🔥  Feature flags cache cleared.');
});
