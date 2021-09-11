# ⛳️ - Handle feature flags within your Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dive-be/laravel-feature-flags.svg?style=flat-square)](https://packagist.org/packages/dive-be/laravel-feature-flags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/dive-be/laravel-feature-flags.svg?style=flat-square)](https://packagist.org/packages/dive-be/laravel-feature-flags)

This package will assist you in flagging certain parts of your application as (in)active.

## What problem does this package solve?

"A feature flag is a software development process used to enable or disable functionality remotely without deploying code. New features can be deployed without making them visible to users. Feature flags help decouple deployment from release letting you manage the full lifecycle of a feature." ([Source](https://launchdarkly.com/blog/what-are-feature-flags))

## Installation

You can install the package via composer:

```shell
composer require dive-be/laravel-feature-flags
```

Once composer has finished, you must publish the configuration and migration:

```shell
php artisan feature:install
```

This is the contents of the published config file:

```php
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
```

If you don't need multi-locale support, you are now good to go.

### Multiple languages support

This package provides first-class support for multiple languages using Spatie's excellent [Laravel translatable package](https://github.com/spatie/laravel-translatable).

```shell
composer require spatie/laravel-translatable
```

Next, go to the configuration file and change `feature_model` to:

```php
Dive\FeatureFlags\Models\TranslatableFeature::class
```

Finally, find the migration and uncomment the comment while also deleting everything in front of it. It should read:

```php
// ...
$table->json('message')->nullable();
$table->timestamp('disabled_at')->nullable();
// ...
```

## Usage

### Setting up your app's initial features

Seeding the (initial) features can be done in a regular Laravel seeder.

```shell
php artisan make:seeder FeaturesTableSeeder
```

Here is an example of what it might look like:

```php
use Dive\FeatureFlags\Models\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('features')->upsert([
            [
                'description' => 'Registrations that come through our partnerships',
                'label' => 'Public registrations',
                'message' => 'The registration period has ended. Thanks for participating in our programme.',
                'name' => 'registrations',
                'scope' => Feature::getDefaultScope(),
            ],
            [
                'description' => 'Display the App version on the homepage',
                'label' => 'Application version',
                'message' => 'Version is hidden',
                'name' => 'version',
                'scope' => Feature::getDefaultScope(),
            ],
        ], ['scope', 'name'], ['description', 'label', 'message']);
    }
}
```

### Resolving the manager

This package provides every possible way to resolve a `Feature` instance out of the IoC container. We've got you covered!

#### Facade

```php
use Dive\FeatureFlags\Facades\Feature;

Feature::find('dashboard');
```

or using the alias (particularly helpful in Blade views)

```php
use Feature;

Feature::disabled('dashboard');
```

#### Helpers

```php
feature('dashboard');
feature_disabled('dashboard');
feature_enabled('dashboard');
feature_verify('dashboard');
```

#### Dependency injection

```php
use Dive\FeatureFlags\Contracts\Feature;

public function index(Feature $feature)
{
    $feature->verify('dashboard');

    return view('layouts.dashboard');
}
```

#### Service Location

```php
app('feature')->find('dashboard');
```

## Blade directives

### @disabled

You can use the `@disabled` directive to conditionally display content in your views depending on a feature's current state. 
Using this directive first will make the feature's `message` property automatically available as a variable inside the block. Examples:

```blade
@disabled('registrations')
    <div class="alert warning">
        {{ $message }} <!-- Automatically injected -->
    </div>
@enabled
    <div class="alert info">
        Welcome to our public registrations.
    </div>
@enddisabled
```

### @enabled

You can also use the `@enabled` directive to do the same thing as above. However, the `$message` variable will **not** be available inside the `@disabled` block when using this directive.

```blade
@enabled('registrations')
    <div class="alert info">text</div>
@disabled
    <div>$message is not available here</div>
@endenabled
```

## Guarding parts of your application

### Route middleware

This package provides a `feature` middleware to guard certain parts of your application. An `AccessDeniedHttpException` (403) will be thrown if the feature is disabled.

```php
Route::middleware('feature:registrations')->get('registrations', RegistrationsController::class);
```

## Artisan commands

### Toggling a feature on/off

Since the features are managed through a `Feature` Eloquent model, you can use solutions such as Laravel Nova. 

However, when developing locally, you might want to easily turn a feature on/off. You can do this using the command below:

```
php artisan feature:toggle {name} {scope?}
```

### Displaying list of all features

Use the command below to display a table of all features and their corresponding state:

```shell
php artisan feature:list
```

Available options: `compact`, `disabled`, `enabled`, `scope`.

### Clearing the cache

The features are cached to speed up subsequent checks. If you don't use Eloquent to update or alter records, you must reset the cache:

```shell
php artisan feature:clear
```

## Testing
```shell
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email oss@dive.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
