<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SubscriptionService
{
    /**
     * Создать подписку для пользователя
     */
    public function createSubscription(User $user, Plan $plan, bool $isTrial = false, ?\App\Models\Invoice $invoice = null): Subscription
    {
        $now = now();
        $trialEndsAt = null;
        $endsAt = null;

        // Проверяем, использовал ли пользователь пробный период для этого тарифа
        if ($isTrial && $plan->trial_days > 0) {
            if ($this->hasUsedTrialForPlan($user, $plan)) {
                // Пробный период уже использован для этого тарифа
                $isTrial = false;
            }
        }

        if ($isTrial && $plan->trial_days > 0) {
            $trialEndsAt = $now->copy()->addDays($plan->trial_days);
            $status = 'trial';
        } else {
            $status = 'active';
            if ($plan->interval === 'monthly') {
                $endsAt = $now->copy()->addMonth();
            } elseif ($plan->interval === 'yearly') {
                $endsAt = $now->copy()->addYear();
            }
        }

        // Если уже есть подписка, обновляем её (явная выборка через query, не relation)
        $subscription = $user->subscription()->first();

        // Сохраняем старый план для проверки изменения
        $oldPlan = $subscription?->plan;

        // Если это смена тарифа (не новая подписка), сохраняем оплаченное время
        $isPlanChange = $subscription && $oldPlan && $oldPlan->id !== $plan->id;
        $preserveEndsAt = false;

        if ($isPlanChange && ! $isTrial) {
            // Если старая подписка еще активна (ends_at в будущем), сохраняем ends_at
            if ($subscription->ends_at && $subscription->ends_at->isFuture()) {
                $preserveEndsAt = true;
                $endsAt = $subscription->ends_at; // Сохраняем старую дату окончания
            }
        }

        // Получаем текущий metadata или создаем новый
        $metadata = $subscription?->metadata ?? [];
        $usedTrials = $metadata['used_trials'] ?? [];

        // Если сохраняем ends_at при смене тарифа, сохраняем информацию о предыдущем тарифе
        if ($preserveEndsAt && $oldPlan) {
            $metadata['previous_plan_id'] = $oldPlan->id;
            $metadata['previous_plan_name'] = $oldPlan->name;
            $metadata['preserved_ends_at'] = $subscription->ends_at->toIso8601String();
        } else {
            // Если не сохраняем ends_at, очищаем информацию о предыдущем тарифе
            unset($metadata['previous_plan_id']);
            unset($metadata['previous_plan_name']);
            unset($metadata['preserved_ends_at']);
        }

        // Если пробный период активирован, добавляем plan_id в список использованных
        if ($isTrial && $plan->trial_days > 0) {
            if (! in_array($plan->id, $usedTrials)) {
                $usedTrials[] = $plan->id;
                $metadata['used_trials'] = $usedTrials;
            }
        }

        $subscriptionData = [
            'plan_id' => $plan->id,
            'status' => $status,
            'starts_at' => $now,
            'ends_at' => $preserveEndsAt ? $subscription->ends_at : $endsAt, // Сохраняем ends_at при смене тарифа
            'trial_ends_at' => $trialEndsAt,
            'cancelled_at' => null,
            'metadata' => $metadata,
        ];

        // Если есть инвойс, связываем его с подпиской
        if ($invoice) {
            $subscriptionData['invoice_id'] = $invoice->id;
            $subscriptionData['payment_status'] = $invoice->isPaid() ? 'paid' : 'pending';
        }

        if ($subscription) {
            $subscription->update($subscriptionData);
        } else {
            $subscriptionData['user_id'] = $user->id;
            $subscription = Subscription::create($subscriptionData);
        }

        // Если инвойс был передан, обновляем его subscription_id
        if ($invoice && ! $invoice->subscription_id) {
            $invoice->update(['subscription_id' => $subscription->id]);
        }

        // Очищаем кеш подписок пользователя
        $user->clearSubscriptionCache();

        // Инициализируем usage для всех метрик тарифа
        $this->initializeUsage($subscription);

        // Проверяем изменение тарифа
        if ($subscription && $oldPlan && $oldPlan->id !== $plan->id) {
            \App\Services\SubscriptionNotificationService::notifyPlanChanged($subscription, $oldPlan, $plan);
        }

        // Проверяем начало пробного периода
        if ($isTrial && $trialEndsAt !== null) {
            \App\Services\SubscriptionNotificationService::notifyTrialStarted($subscription);
        }

        return $subscription;
    }

    /**
     * Инициализировать usage для всех метрик тарифа
     */
    protected function initializeUsage(Subscription $subscription): void
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();

        foreach ($subscription->plan->features as $feature) {
            // Инициализируем usage только для месячных метрик
            if ($feature->feature_type === 'integer' && $this->isMonthlyMetric($feature->feature_key)) {
                SubscriptionUsage::firstOrCreate(
                    [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'feature_key' => $feature->feature_key,
                        'period_start' => $periodStart,
                    ],
                    [
                        'current_usage' => 0,
                        'period_end' => $periodEnd,
                    ]
                );
            }
        }
    }

    /**
     * Проверить лимит для метрики
     */
    public function checkLimit(User $user, string $featureKey): bool
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return false;
        }

        $limit = $this->getLimit($user, $featureKey);

        // Если лимит null - метрика недоступна
        if ($limit === null) {
            return false;
        }

        // Если лимит -1 - безлимит
        if ($limit === -1) {
            return true;
        }

        $currentUsage = $this->getCurrentUsage($user, $featureKey);

        return $currentUsage < $limit;
    }

    /**
     * Получить текущее использование метрики
     * Uses caching to avoid repeated DB queries.
     */
    public function getCurrentUsage(User $user, string $featureKey): int
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return 0;
        }

        // Для месячных метрик используем usage с кешированием (3 минуты)
        if ($this->isMonthlyMetric($featureKey)) {
            $cacheKey = "usage_{$user->id}_{$featureKey}_".now()->format('Y-m');

            return Cache::remember($cacheKey, 180, function () use ($user, $featureKey) {
                $usage = SubscriptionUsage::where('user_id', $user->id)
                    ->where('feature_key', $featureKey)
                    ->where('period_start', '<=', now())
                    ->where('period_end', '>=', now())
                    ->first();

                return $usage ? $usage->current_usage : 0;
            });
        }

        // Для остальных метрик кешируем на 3 минуты (критично для лимитов!)
        $cacheKey = "usage_{$user->id}_{$featureKey}";

        return Cache::remember($cacheKey, 180, function () use ($user, $featureKey) {
            return match ($featureKey) {
                'max_locations' => $user->businesses()->withCount('locations')->get()->sum('locations_count'),
                'max_masters' => $user->businesses()->withCount('masters')->get()->sum('masters_count'),
                'max_services' => $user->businesses()->withCount('services')->get()->sum('services_count'),
                'max_clients' => $user->businesses()->withCount('clients')->get()->sum('clients_count'),
                'max_business_users' => $this->getBusinessUsersCount($user),
                default => 0,
            };
        });
    }

    /**
     * Получить лимит для метрики
     */
    public function getLimit(User $user, string $featureKey): int|bool|null
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return null;
        }

        return $subscription->getFeatureLimit($featureKey);
    }

    /**
     * Получить usage и limits для нескольких метрик одним запросом (оптимизация N+1)
     */
    public function getMultipleUsageAndLimits(User $user, array $featureKeys): array
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return array_fill_keys($featureKeys, [
                'current' => 0,
                'limit' => 0,
                'percentage' => 0,
                'warning' => false,
            ]);
        }

        // Получаем все usage для месячных метрик одним запросом
        $monthlyKeys = array_filter($featureKeys, fn ($key) => $this->isMonthlyMetric($key));

        $usageData = [];
        if (! empty($monthlyKeys)) {
            $usages = SubscriptionUsage::where('user_id', $user->id)
                ->whereIn('feature_key', $monthlyKeys)
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now())
                ->get()
                ->keyBy('feature_key');

            foreach ($monthlyKeys as $key) {
                $usageData[$key] = $usages->get($key)?->current_usage ?? 0;
            }
        }

        // Для остальных метрик считаем напрямую
        foreach ($featureKeys as $key) {
            if (! isset($usageData[$key])) {
                $usageData[$key] = $this->getCurrentUsage($user, $key);
            }
        }

        // Получаем limits для всех метрик (они уже закешированы в subscription)
        $result = [];
        foreach ($featureKeys as $key) {
            $current = $usageData[$key] ?? 0;
            $limit = $subscription->getFeatureLimit($key);

            $result[$key] = [
                'current' => $current,
                'limit' => $limit,
                'percentage' => $limit > 0 ? round(($current / $limit) * 100, 1) : 0,
                'warning' => $limit > 0 && ($current / $limit) > 0.8,
            ];
        }

        return $result;
    }

    /**
     * Увеличить использование метрики
     */
    public function incrementUsage(User $user, string $featureKey, int $amount = 1): void
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return;
        }

        // Для месячных метрик обновляем usage
        if ($this->isMonthlyMetric($featureKey)) {
            $periodStart = now()->startOfMonth();
            $periodEnd = now()->endOfMonth();

            $usage = SubscriptionUsage::firstOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'feature_key' => $featureKey,
                    'period_start' => $periodStart,
                ],
                [
                    'current_usage' => 0,
                    'period_end' => $periodEnd,
                ]
            );

            $usage->increment('current_usage', $amount);

            // Очищаем кеш
            $cacheKey = "usage_{$user->id}_{$featureKey}_".now()->format('Y-m');
            Cache::forget($cacheKey);
        } else {
            // Очищаем кеш для немесячных метрик
            Cache::forget("usage_{$user->id}_{$featureKey}");
        }
    }

    /**
     * Уменьшить использование метрики
     */
    public function decrementUsage(User $user, string $featureKey, int $amount = 1): void
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return;
        }

        // Для месячных метрик обновляем usage
        if ($this->isMonthlyMetric($featureKey)) {
            $usage = SubscriptionUsage::where('user_id', $user->id)
                ->where('feature_key', $featureKey)
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now())
                ->first();

            if ($usage) {
                $usage->decrement('current_usage', $amount);
                if ($usage->current_usage < 0) {
                    $usage->update(['current_usage' => 0]);
                }
            }

            // Очищаем кеш
            $cacheKey = "usage_{$user->id}_{$featureKey}_".now()->format('Y-m');
            Cache::forget($cacheKey);
        } else {
            // Очищаем кеш для немесячных метрик
            Cache::forget("usage_{$user->id}_{$featureKey}");
        }
    }

    /**
     * Сбросить месячные метрики
     */
    public function resetMonthlyUsage(User $user): void
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return;
        }

        // Сбрасываем только месячные метрики
        SubscriptionUsage::where('user_id', $user->id)
            ->where('period_end', '<', now()->startOfMonth())
            ->where(function ($query) {
                $query->where('feature_key', 'max_appointments_per_month')
                    ->orWhere('feature_key', 'like', '%_per_month')
                    ->orWhere('feature_key', 'like', '%_monthly');
            })
            ->update([
                'current_usage' => 0,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
            ]);
    }

    /**
     * Проверить возможность создания локации
     */
    public function canCreateLocation(User $user): bool
    {
        return $this->checkLimit($user, 'max_locations');
    }

    /**
     * Проверить возможность создания мастера
     */
    public function canCreateMaster(User $user): bool
    {
        return $this->checkLimit($user, 'max_masters');
    }

    /**
     * Проверить возможность создания услуги
     */
    public function canCreateService(User $user): bool
    {
        return $this->checkLimit($user, 'max_services');
    }

    /**
     * Проверить возможность создания клиента
     */
    public function canCreateClient(User $user): bool
    {
        return $this->checkLimit($user, 'max_clients');
    }

    /**
     * Проверить возможность создания записи
     */
    public function canCreateAppointment(User $user): bool
    {
        return $this->checkLimit($user, 'max_appointments_per_month');
    }

    /**
     * Проверить возможность создания пользователя бизнеса
     */
    public function canCreateBusinessUser(User $user): bool
    {
        return $this->checkLimit($user, 'max_business_users');
    }

    /**
     * Получить количество пользователей бизнеса (исключая владельца)
     */
    protected function getBusinessUsersCount(User $user): int
    {
        $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
        if (! $ownerRole) {
            return 0;
        }

        $totalUsers = 0;
        foreach ($user->businesses as $business) {
            $usersCount = $business->users()
                ->wherePivot('role_id', '!=', $ownerRole->id)
                ->count();
            $totalUsers += $usersCount;
        }

        return $totalUsers;
    }

    /**
     * Проверить, является ли метрика месячной
     */
    protected function isMonthlyMetric(string $featureKey): bool
    {
        return str_contains($featureKey, '_per_month') || str_contains($featureKey, '_monthly');
    }

    /**
     * Проверить, использовал ли пользователь пробный период для конкретного тарифа
     */
    /**
     * Проверить использование trial для нескольких планов сразу (оптимизация N+1)
     */
    public function hasUsedTrialForPlans(User $user, $plans): array
    {
        $subscription = $user->subscription;

        if (! $subscription) {
            return array_fill_keys($plans->pluck('id')->toArray(), false);
        }

        $metadata = $subscription->metadata ?? [];
        $usedTrials = $metadata['used_trials'] ?? [];

        $result = [];
        foreach ($plans as $plan) {
            // Проверяем в metadata
            if (in_array($plan->id, $usedTrials)) {
                $result[$plan->id] = true;

                continue;
            }

            // Дополнительная проверка для текущей подписки
            if ($subscription->plan_id === $plan->id && $subscription->trial_ends_at !== null && $subscription->trial_ends_at->isPast()) {
                $result[$plan->id] = true;
            } else {
                $result[$plan->id] = false;
            }
        }

        return $result;
    }

    public function hasUsedTrialForPlan(User $user, Plan $plan): bool
    {
        $subscription = $user->subscription;

        if (! $subscription) {
            return false;
        }

        // Проверяем metadata для истории использованных пробных периодов
        $metadata = $subscription->metadata ?? [];
        $usedTrials = $metadata['used_trials'] ?? [];

        // Если plan_id уже в списке использованных пробных периодов
        if (in_array($plan->id, $usedTrials)) {
            return true;
        }

        // Дополнительная проверка для обработки существующих данных:
        // Если текущая подписка имеет trial_ends_at для этого plan_id
        if ($subscription->plan_id === $plan->id && $subscription->trial_ends_at !== null) {
            // Если пробный период уже закончился, значит он был использован
            if ($subscription->trial_ends_at->isPast()) {
                // Обновляем metadata для будущих проверок
                if (! in_array($plan->id, $usedTrials)) {
                    $usedTrials[] = $plan->id;
                    $metadata['used_trials'] = $usedTrials;
                    $subscription->update(['metadata' => $metadata]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Отменить подписку пользователя
     * Подписка остается активной до окончания периода (ends_at)
     */
    public function cancelSubscription(User $user): bool
    {
        $subscription = $user->activeSubscription();

        // Проверяем наличие активной подписки
        if (! $subscription) {
            return false;
        }

        // Проверяем, что тариф не бесплатный
        if ($subscription->plan->slug === 'free') {
            return false;
        }

        // Проверяем, что подписка еще не отменена
        if ($subscription->cancelled_at !== null) {
            return false;
        }

        // Устанавливаем cancelled_at, статус остается active
        $subscription->update([
            'cancelled_at' => now(),
        ]);

        return true;
    }
}
