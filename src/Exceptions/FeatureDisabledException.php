<?php

namespace Dive\FeatureFlags\Exceptions;

use Dive\FeatureFlags\Contracts\Feature;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FeatureDisabledException extends AccessDeniedHttpException
{
    public static function make(Feature $feature): self
    {
        return new self($feature->getMessage());
    }
}
