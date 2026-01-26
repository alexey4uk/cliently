<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SubscriptionMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'description',
        'icon',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope для фильтрации активных метрик
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для сортировки по sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get cached list of active metrics ordered by sort_order.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveCached()
    {
        return Cache::remember('subscription_metrics_active', 3600, function () {
            return static::where('is_active', true)
                ->ordered()
                ->get();
        });
    }

    /**
     * Clear subscription metrics cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('subscription_metrics_active');
    }
}
