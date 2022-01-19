<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Database\Factories;

use Dive\FeatureFlags\Models\TranslatableFeature;

class TranslatableFeatureFactory extends FeatureFactory
{
    protected $model = TranslatableFeature::class;

    public function isDisabled(): self
    {
        return $this->state([
            'disabled_at' => now(),
            'message' => [app()->getLocale() => $this->faker->text],
        ]);
    }

    public function withMessage(string $message): self
    {
        return $this->state(['message' => [app()->getLocale() => $message]]);
    }
}
