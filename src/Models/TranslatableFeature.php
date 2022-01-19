<?php declare(strict_types=1);

namespace Dive\FeatureFlags\Models;

use Dive\FeatureFlags\Database\Factories\TranslatableFeatureFactory;
use Spatie\Translatable\HasTranslations;

class TranslatableFeature extends Feature
{
    use HasTranslations;

    protected $table = 'features';

    protected array $translatable = ['message'];

    protected static function newFactory()
    {
        return TranslatableFeatureFactory::new();
    }
}
