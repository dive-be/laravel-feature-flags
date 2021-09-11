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

For a full list of what's available to you, please refer to the _[Feature contract](https://github.com/dive-be/laravel-feature-flags/blob/master/src/Contracts/Feature.php)_ for an exhaustive list.

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

## Scoping

The package allows you to define a custom scope along with the feature (not to be confused with Laravel's global scopes). 
Particularly useful when you need to use the same name for different parts of your application. 
Most of the package's functions/methods accept an additional `$scope` argument.

Refer to the [Feature contract](https://github.com/dive-be/laravel-feature-flags/blob/master/src/Contracts/Feature.php) for an exhaustive list.

### Changing the default wildcard scope

If you wish to use a different scope rather than the `*` sign the package uses by default when creating and checking/verifying features, you may change this in your `AppServiceProvider`'s `boot` method:

```php
use Dive\FeatureFlags\Models\Feature;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Feature::setDefaultScope('my-wonderful-app');
    }
}
```

### Seeding 

Refer to the [seeding section](#setting-up-your-apps-initial-features) above on how to scope your features.

## Blade directives 🗡

### @disabled

You can use the `@disabled` directive to conditionally display content in your views depending on a feature's current state.
Using this directive first will make the feature's `message` property automatically available as a scoped variable inside the block. Examples:

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

You can also use the `@enabled` directive to do the same thing as above. However, the `$message` variable will **not** be available inside the `@disabled` block when using this directive __first__.

```blade
@enabled('registrations')
    <div class="alert info">text</div>
@disabled
    <div>$message is not available here</div>
@endenabled
```

### @can(not)

This package also registers itself at [Laravel's Gate](https://laravel.com/docs/8.x/authorization#gates). The visitor does not have to be auhenticated in order to use it:

```php
@can('feature', 'dashboard')
    <a href="/dashboard" target="_blank">View Dashboard</a>
@else
    <small>Dashboard is currently disabled</small>
@endcan
```

## Guarding parts of your application 💂🏼

There are multiple ways to prevent users from accessing disabled parts of your application.

A `FeatureDisabledException` will be thrown in all cases if the feature is not enabled.

### Route middleware

```php
Route::get('registrations', [RegistrationsController::class, 'index'])->middleware('feature:registrations');
```

### Manual checking using 'verify'

#### Controller example

Assume your typical controller:

```php
class RegistrationsController extends Controller
{
    public function __construct(Feature $feature)
    {
        $feature->verify('registrations');
    }
    
    public function index()
    {
        return view('registrations.index');
    }
}
```

#### Livewire example

This is especially useful in contexts where you cannot really use route middleware, such as [Livewire actions](https://laravel-livewire.com/docs/2.x/actions):

```php
use Dive\FeatureFlags\Facades\Feature;
use Dive\Wishlist\Facades\Wishlist;

class HeartButton extends Component
{
    public function add($id)
    {
        Feature::verify('wishlist');
        
        Wishlist::add($id);
    }

    public function render()
    {
        return view('livewire.heart-button');
    }
}
```

> PS: Be sure to check out our [Wishlist package](https://github.com/dive-be/laravel-wishlist) as well 😉

### Access Gate

This package also registers itself at [Laravel's Gate](https://laravel.com/docs/8.x/authorization#gates) providing you the ability to check a feature's state through them. This means that you can do things like the following:

```php
Route::get(...)->middleware('can:feature,dashboard');
```

```php
Gate::authorize('feature', 'dashboard');
```

**However**, there is one key difference. Laravel's Gate will throw an `AccessDeniedHttpException`, while the package's own checks will throw a `FeatureDisabledException` (which extends the former exception class). So, if you need to know the exception's type, you are highly adviced **not** to use gates.

## Artisan commands 🧑‍🎨

### Toggling a feature on/off

Since the features are managed through a `Feature` Eloquent model, you can definitely use solutions such as Laravel Nova to do this.

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

The features are cached forever to speed up subsequent checks. If you don't use Eloquent to update or alter records, you must reset the cache manually:

```shell
php artisan feature:clear
```

> Note: when you create/update features through Eloquent, the cache busting is done for you automatically.

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
