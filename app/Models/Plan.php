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

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Получить значение метрики тарифа
     */
    public function getFeatureValue(string $key): mixed
    {
        // Ищем фичу через связь, фильтруя по колонке 'key' в таблице метрик
        $feature = $this->features()
            ->whereHas('metric', function ($query) use ($key) {
                $query->where('key', $key);
            })
            ->first();

        if (!$feature) {
            return null;
        }

        // Тип теперь берем из связанной метрики, а значение из колонки 'value'
        return match ($feature->metric->type) {
            'boolean' => filter_var($feature->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $feature->value,
            default => $feature->value,
        };
    }


    /**
     * Проверить наличие метрики
     */
    public function hasFeature(string $key): bool
    {
        return $this->features()
            ->whereHas('metric', fn ($q) => $q->where('key', $key))
            ->exists();
    }

    /**
     * Get list of active plans ordered by sort_order.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveCached()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
