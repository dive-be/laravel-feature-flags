<?php

namespace Tests;

use function Pest\Laravel\artisan;

it('copies the config', function () {
    unlink($path = config_path('feature-flags.php'));

    artisan('feature:install')->execute();

    expect(file_exists($path))->toBeTrue();
});

it('copies the migration', function () {
    array_map('unlink', glob($path = database_path('migrations/*_create_features_table.php')));

    artisan('feature:install')->execute();

    expect(glob($path))->toHaveCount(1);
});
