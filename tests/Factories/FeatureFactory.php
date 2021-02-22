<?php

namespace Tests\Factories;

use Dive\FeatureFlags\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition()
    {
        return [
            'description' => $this->faker->text,
            'label' => $this->faker->words(3, true),
            'name' => $this->faker->name,
        ];
    }

    public function isDisabled(): self
    {
        return $this->state([
            'disabled_at' => now(),
            'message' => $this->faker->text,
        ]);
    }

    public function withLabel(string $label): self
    {
        return $this->state(compact('label'));
    }

    public function withName(string $name): self
    {
        return $this->state(compact('name'));
    }

    public function withScope(string $scope): self
    {
        return $this->state(compact('scope'));
    }
}
