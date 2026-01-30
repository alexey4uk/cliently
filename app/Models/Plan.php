<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'interval',
        'trial_days',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Получить значение метрики тарифа (с кешированием)
     */
    public function getFeatureValue(string $key): mixed
    {
        $cacheKey = "plan_{$this->id}_feature_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key) {
            $feature = $this->features()->where('feature_key', $key)->first();

            if (! $feature) {
                return null;
            }

            return match ($feature->feature_type) {
                'boolean' => $feature->feature_value === 'true',
                'integer' => (int) $feature->feature_value,
                default => $feature->feature_value,
            };
        });
    }

    /**
     * Проверить наличие метрики (с кешированием)
     */
    public function hasFeature(string $key): bool
    {
        $cacheKey = "plan_{$this->id}_has_feature_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key) {
            return $this->features()->where('feature_key', $key)->exists();
        });
    }

    /**
     * Очистить кеш features для плана
     */
    public function clearFeaturesCache(): void
    {
        // Очищаем все features для этого плана
        $features = $this->features;
        foreach ($features as $feature) {
            Cache::forget("plan_{$this->id}_feature_{$feature->feature_key}");
            Cache::forget("plan_{$this->id}_has_feature_{$feature->feature_key}");
        }
    }

    /**
     * Get cached list of active plans ordered by sort_order.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveCached()
    {
        return Cache::remember('plans_active_list', 3600, function () {
            return static::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Clear plans cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('plans_active_list');
    }

    /**
     * Boot method to clear cache on model changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($plan) {
            static::clearCache();
        });

        static::deleted(function ($plan) {
            static::clearCache();
        });
    }
}
