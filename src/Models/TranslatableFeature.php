<?php

namespace Dive\FeatureFlags\Models;

use Dive\FeatureFlags\Database\Factories\TranslatableFeatureFactory;
use Spatie\Translatable\HasTranslations;

class TranslatableFeature extends Feature
{
    use HasTranslations;

    protected array $translatable = ['message'];

    protected static function newFactory()
    {
        return TranslatableFeatureFactory::new();
    }
}
