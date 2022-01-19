<?php declare(strict_types=1);

namespace Tests;

use function Pest\Laravel\artisan;

it('clears the cache', function () {
    artisan('feature:clear')
        ->assertExitCode(0)
        ->expectsOutput('🔥  Feature flags cache cleared.');
});
