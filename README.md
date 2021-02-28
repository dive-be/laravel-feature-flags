# Laravel Feature Flags

This package will assist you in flagging certain parts of your application as (in)active.

⚠️ Minor releases of this package may cause breaking changes as it has no stable release yet.

## What problem does this package solve?

"A feature flag is a software development process used to enable or disable functionality remotely without deploying code. New features can be deployed without making them visible to users. Feature flags help decouple deployment from release letting you manage the full lifecycle of a feature." ([Source](https://launchdarkly.com/blog/what-are-feature-flags))

## Installation

```shell
composer require dive-be/laravel-feature-flags
```

Once composer has finished, you must publish the configuration and migration:

```shell
php artisan feature:install
```

If you don't need multi-locale support, you are now good to go.

### Multiple languages support

This package provides first-class support for multiple languages using Spatie's excellent [Laravel translatable package](https://github.com/spatie/laravel-translatable).

```shell
composer require spatie/laravel-translatable
```

Next, find the configuration file and change `feature_model` to:

```php
return [
    'feature_model' => Dive\FeatureFlags\Models\TranslatableFeature::class,
];
```

Finally, find the migration and uncomment the comment while also deleting everything in front of it. It should read:

```php
// ...
$table->json('message')->nullable();
$table->timestamp('disabled_at')->nullable();
// ...
```

## Setting up your app's initial features

Seeding the (initial) features can be done in a regular Laravel seeder.

```shell
php artisan make:seeder FeaturesTableSeeder
```

Here is an example of what it might look like:

```php
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
                'scope' => '*',
            ],
            [
                'description' => 'Display the App version on the homepage',
                'label' => 'Application version',
                'message' => 'Version is hidden',
                'name' => 'version',
                'scope' => '*',
            ],
        ], ['scope', 'name'], ['description', 'label', 'message']);
    }
}
```

## Usage

### Checking a feature's state

There are lots of ways to check whether a feature is enabled/disabled.

#### Facade

```php
use Dive\FeatureFlags\Facades\Feature;

Feature::disabled('registrations');
Feature::enabled('registrations');
```

#### Gate

```php
use Illuminate\Support\Facades\Gate;

Gate::check('feature', 'registrations');
```

#### Helper functions

```php
feature_disabled('registrations');
feature_enabled('registrations');
```

#### Container binding

```php
use Dive\FeatureFlags\Contracts\Feature;

// Somewhere dependency injected
public function handle(Feature $feature)
{
    $feature->disabled('registrations');
    $feature->enabled('registrations');
}
```

### Blade templates

You can use the `@disabled` or `@enabled` directives to conditionally display content in your views depending on a feature's current state. 
Using the `@disabled` directive first will make a feature's `message` property automatically available. Examples:

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

```blade
@enabled('registrations')
    <div class="alert info">text</div>
@disabled
    <div>$message is not available here</div>
@endenabled
```

### Route protection

This package provides a `feature` middleware to guard certain parts of your application. An `AccessDeniedHttpException` (403) will be thrown if the feature is disabled.

```php
Route::middleware('feature:registrations')->get('registrations', RegistrationsController::class);
```

### Toggling a feature on/off

Since the features are managed through a `Feature` Eloquent model, you may use solutions such as Laravel Nova. 

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

You can run tests with:

```shell
composer test
```

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [Nico Verbruggen](https://github.com/nicoverbruggen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
