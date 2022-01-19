<?php declare(strict_types=1);

namespace Tests;

use function Pest\Laravel\artisan;

afterEach(function () {
    file_exists(config_path('feature-flags.php')) && unlink(config_path('feature-flags.php'));
    array_map('unlink', glob(database_path('migrations/*_create_features_table.php')));
});

it('copies the config', function () {
    artisan('feature:install')->execute();

    expect(file_exists(config_path('feature-flags.php')))->toBeTrue();
});

it('copies the migration', function () {
    artisan('feature:install')->execute();

    expect(glob(database_path('migrations/*_create_features_table.php')))->toHaveCount(1);
});
