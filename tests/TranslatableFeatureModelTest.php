<?php

namespace Tests;

use Dive\FeatureFlags\Models\Feature;
use Dive\FeatureFlags\Models\TranslatableFeature;
use Spatie\Translatable\HasTranslations;

beforeEach(function () {
    $this->model = new TranslatableFeature();
});

it('can create a new model via factories', function () {
    expect(TranslatableFeature::factory()->make())->toBeInstanceOf(TranslatableFeature::class);
});

it('extends the base model', function () {
    expect($this->model)->toBeInstanceOf(Feature::class);
});

it('has the correct translatable attributes', function () {
    expect($this->model->getTranslatableAttributes())->toBe(['message']);
});

it('uses the translatables trait', function () {
    expect(class_uses($this->model))->toContain(HasTranslations::class);
});
