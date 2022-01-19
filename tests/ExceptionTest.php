<?php declare(strict_types=1);

namespace Tests;

use Dive\FeatureFlags\Exceptions\UnknownFeatureException;

test('unknown feature is makeable', function () {
    $exception = UnknownFeatureException::make('dive', 'interactive');

    expect($exception->getMessage())->toBe('The requested feature interactive:dive could not be found.');
});
