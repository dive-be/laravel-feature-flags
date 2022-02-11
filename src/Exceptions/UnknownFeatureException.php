<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Exceptions;

use Exception;

class UnknownFeatureException extends Exception
{
    public static function make(string $name, string $scope): self
    {
        return new self("The requested feature {$scope}:{$name} could not be found.");
    }
}
