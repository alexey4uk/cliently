<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Получить значение метрики тарифа
     */
    public function getFeatureValue(string $key): mixed
    {
        $feature = $this->features()->where('feature_key', $key)->first();

        if (! $feature) {
            return null;
        }

        return match ($feature->feature_type) {
            'boolean' => $feature->feature_value === 'true',
            'integer' => (int) $feature->feature_value,
            default => $feature->feature_value,
        };
    }

    /**
     * Проверить наличие метрики
     */
    public function hasFeature(string $key): bool
    {
        return $this->features()->where('feature_key', $key)->exists();
    }
}
