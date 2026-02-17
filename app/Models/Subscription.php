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
        'invoice_id',
        'payment_status',
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

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Проверить, активна ли подписка. Статусами управляет крон.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Проверить, является ли подписка пробной. Статусами управляет крон.
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
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

    /**
     * Получить эффективный план (если есть сохраненный предыдущий план и ends_at еще не истек)
     * Используется при смене тарифа с сохранением оплаченного времени
     */
    public function getEffectivePlan(): Plan
    {
        $metadata = $this->metadata ?? [];
        $previousPlanId = $metadata['previous_plan_id'] ?? null;

        // Если есть предыдущий план и подписка ещё активна (статус управляется кроном) — используем предыдущий план
        if ($previousPlanId && $this->ends_at && in_array($this->status, ['active', 'trial'], true)) {
            $previousPlan = Plan::find($previousPlanId);
            if ($previousPlan) {
                return $previousPlan;
            }
        }

        // Иначе используем текущий план
        return $this->plan;
    }

    /**
     * Проверить, может ли использовать метрику (с учетом эффективного плана)
     */
    public function canUseFeature(string $key): bool
    {
        return $this->getEffectivePlan()->hasFeature($key);
    }

    /**
     * Получить лимит для метрики (с учетом эффективного плана)
     */
    public function getFeatureLimit(string $key): int|bool|null
    {
        return $this->getEffectivePlan()->getFeatureValue($key);
    }
}
