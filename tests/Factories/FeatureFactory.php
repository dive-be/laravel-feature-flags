<?php

namespace Tests\Factories;

use Dive\FeatureFlags\Feature;
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
            'message' => [app()->getLocale() => $this->faker->text],
        ]);
    }
}
