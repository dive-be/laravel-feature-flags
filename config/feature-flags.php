<?php

return [

    /**
     * The name of the cache key that will be used to cache your app's features.
     */
    'cache_key' => 'feature_flags',

    /**
     * The feature model that will be used to retrieve your app's features.
     */
    'feature_model' => Dive\FeatureFlags\Models\Feature::class,
];
