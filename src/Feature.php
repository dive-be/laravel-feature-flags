<?php

namespace Dive\FeatureFlags;

use Dive\FeatureFlags\Exceptions\UnknownFeatureException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

/**
 * @property string              $description
 * @property \Carbon\Carbon|null $disabled_at
 * @property bool                $is_enabled
 * @property string              $label
 * @property string|null         $message
 * @property string              $name
 * @property string              $scope
 * @property string              $unique_name
 */
class Feature extends Model
{
    use HasTranslations;

    private const CACHE = 'feature_flags';

    private const GENERAL = '*';

    public $timestamps = false;

    protected $casts = ['disabled_at' => 'datetime'];

    protected $guarded = [];

    protected $translatable = ['message'];

    public static function disabled(string $name, ?string $scope = null): bool
    {
        return ! self::enabled($name, $scope);
    }

    public static function enabled(string $name, ?string $scope = null): bool
    {
        return self::find($name, $scope)->is_enabled;
    }

    /**
     * @throws UnknownFeatureException
     */
    public static function find(string $name, ?string $scope = null): self
    {
        $scope ??= self::GENERAL;

        $feature = Cache::rememberForever(self::CACHE, static function () {
            return self::all()->keyBy(fn (self $feature) => $feature->unique_name);
        })->get((new self(compact('name', 'scope')))->unique_name);

        if (! $feature instanceof self) {
            throw UnknownFeatureException::make($name, $scope);
        }

        return $feature;
    }

    protected static function booted()
    {
        self::creating(fn (self $model) => $model->scope ??= self::GENERAL);
        self::saved(fn () => Cache::forget(self::CACHE));
    }

    public function getIsEnabledAttribute(): bool
    {
        return is_null($this->disabled_at);
    }

    public function getUniqueNameAttribute(): string
    {
        return $this->scope.'.'.$this->name;
    }

    public function toggle(): bool
    {
        $this->update(['disabled_at' => $this->is_enabled ? now() : null]);

        return $this->is_enabled;
    }

    public function __toString()
    {
        return $this->label;
    }
}
