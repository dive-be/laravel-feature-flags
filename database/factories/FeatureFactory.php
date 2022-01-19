<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Database\Factories;

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

    public function withDescription(string $description): self
    {
        return $this->state(compact('description'));
    }

    public function withLabel(string $label): self
    {
        return $this->state(compact('label'));
    }

    public function withMessage(string $message): self
    {
        return $this->state(compact('message'));
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
