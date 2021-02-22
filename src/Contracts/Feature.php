<?php

namespace Dive\FeatureFlags\Contracts;

interface Feature
{
    public function disabled(string $name, ?string $scope = null): bool;

    public function enabled(string $name, ?string $scope = null): bool;

    public function find(string $name, ?string $scope = null): self;

    public function getIsEnabledAttribute(): bool;

    public function getUniqueNameAttribute(): string;

    public function toggle(): bool;
}
