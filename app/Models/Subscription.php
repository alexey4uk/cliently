<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    /**
     * Проверить, активна ли подписка
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Проверить, является ли подписка пробной
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Проверить, может ли использовать метрику
     */
    public function canUseFeature(string $key): bool
    {
        return $this->plan->hasFeature($key);
    }

    /**
     * Получить лимит для метрики
     */
    public function getFeatureLimit(string $key): int|bool|null
    {
        return $this->plan->getFeatureValue($key);
    }

    /**
     * Проверить, отменена ли подписка
     */
    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    /**
     * Проверить, будет ли подписка отменена в конце периода
     */
    public function willCancelAtEnd(): bool
    {
        return $this->isCancelled() && $this->isActive();
    }
}
