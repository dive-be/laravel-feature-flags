<?php

namespace Dive\FeatureFlags\Models;

use Spatie\Translatable\HasTranslations;

class TranslatableFeature extends Feature
{
    use HasTranslations;

    protected array $translatable = ['message'];
}
