<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Exceptions;

use Dive\FeatureFlags\Contracts\Feature;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FeatureDisabledException extends AccessDeniedHttpException
{
    public Feature $feature;

    public static function make(Feature $feature): self
    {
        return (new self($feature->getMessage() ?? ''))->withFeature($feature);
    }

    public function withFeature(Feature $feature): self
    {
        $this->feature = $feature;

        return $this;
    }
}
