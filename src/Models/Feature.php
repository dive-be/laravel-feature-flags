<?php

namespace Dive\FeatureFlags\Models;

use Dive\FeatureFlags\Contracts\Feature as Contract;
use Dive\FeatureFlags\Database\Factories\FeatureFactory;
use Dive\FeatureFlags\Events\FeatureToggled;
use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * @property string              $description
 * @property \Carbon\Carbon|null $disabled_at
 * @property bool                $is_enabled
 * @property string              $label
 * @property string|null         $message
 * @property string              $name
 * @property string              $scope
 * @property string              $state
 * @property string              $unique_name
 */
class Feature extends Model implements Contract
{
    use HasFactory;

    public const GENERAL = '*';

    public $timestamps = false;

    protected $casts = ['disabled_at' => 'datetime'];

    protected $guarded = [];

    protected static function booted()
    {
        self::creating(fn (self $model) => $model->scope ??= self::GENERAL);
        self::saved(fn () => Cache::forget(Config::get('feature-flags.cache_key')));
    }

    protected static function newFactory()
    {
        return FeatureFactory::new();
    }

    public function getStateAttribute(): string
    {
        return $this->isEnabled() ? '<fg=green>enabled</>' : '<fg=red>disabled</>';
    }

    public function getUniqueNameAttribute(): string
    {
        return $this->scope.'.'.$this->name;
    }

    public function disabled(string $name, ?string $scope = null): bool
    {
        return ! $this->enabled($name, $scope);
    }

    public function enabled(string $name, ?string $scope = null): bool
    {
        return $this->find($name, $scope)->isEnabled();
    }

    /**
     * @throws UnknownFeatureException
     */
    public function find(string $name, ?string $scope = null): self
    {
        $scope ??= self::GENERAL;

        $feature = Cache::rememberForever(Config::get('feature-flags.cache_key'), function () {
            return self::all()->keyBy(fn (self $feature) => $feature->unique_name);
        })->get((new self(compact('name', 'scope')))->unique_name);

        if (! $feature instanceof self) {
            throw UnknownFeatureException::make($name, $scope);
        }

        return $feature;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFeatures(): Collection
    {
        return $this
            ->newQuery()
            ->orderBy('scope')
            ->orderBy('name')
            ->get();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getState(): string
    {
        return $this->getStateAttribute();
    }

    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    public function isEnabled(): bool
    {
        return is_null($this->disabled_at);
    }

    public function toggle(): bool
    {
        $this->update(['disabled_at' => $this->isEnabled() ? now() : null]);

        self::$dispatcher->dispatch(FeatureToggled::make($this));

        return $this->isEnabled();
    }

    public function __toString()
    {
        return $this->label;
    }
}
