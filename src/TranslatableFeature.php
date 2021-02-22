<?php

namespace Dive\FeatureFlags;

use Spatie\Translatable\HasTranslations;

class TranslatableFeature extends Feature
{
    use HasTranslations;

    protected array $translatable = ['message'];
}
