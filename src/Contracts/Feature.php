<?php

namespace Dive\FeatureFlags\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface Feature
{
    public function disabled(string $name, ?string $scope = null): bool;

    public function enabled(string $name, ?string $scope = null): bool;

    public function find(string $name, ?string $scope = null): self;

    public function getDescription(): string;

    public function getFeatures(): Collection;

    public function getLabel(): string;

    public function getMessage(): ?string;

    public function isDisabled(): bool;

    public function isEnabled(): bool;

    public function toggle(): bool;
}
