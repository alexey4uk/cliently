<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\User;

class SubscriptionService
{
    /**
     * Стандартное сообщение при достижении лимита тарифа (для единообразия ответов).
     */
    public static function planLimitErrorMessage(): string
    {
        return 'Достигнут лимит для вашего тарифа. Обновите тариф для увеличения лимита.';
    }

    /**
     * Создать подписку для пользователя
     */
    public function createSubscription(
        User $user,
        Plan $plan,
        bool $isTrial = false,
        ?\App\Models\Invoice $invoice = null,
    ): Subscription {
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

        $isFreePlan = $plan->slug === 'free';

        if ($isTrial && $plan->trial_days > 0) {
            $trialEndsAt = $now->copy()->addDays($plan->trial_days);
            $status = 'trial';
        } else {
            $status = 'active';
            // Бесплатный тариф не имеет срока окончания — подписка действует бессрочно (без продления)
            if (! $isFreePlan) {
                if ($plan->interval === 'monthly') {
                    $endsAt = $now->copy()->addMonth();
                } elseif ($plan->interval === 'yearly') {
                    $endsAt = $now->copy()->addYear();
                }
            }
        }

        // Если уже есть подписка, обновляем её (явная выборка через query, не relation)
        $subscription = $user->subscription()->first();

        // Сохраняем старый план для проверки изменения
        $oldPlan = $subscription?->plan;

        // Если это смена тарифа (не новая подписка), сохраняем оплаченное время только при понижении
        $isPlanChange = $subscription && $oldPlan && $oldPlan->id !== $plan->id;
        $preserveEndsAt = false;

        if ($isPlanChange && ! $isTrial) {
            $oldPrice = $oldPlan->price !== null ? (float) $oldPlan->price : 0;
            $newPrice = $plan->price !== null ? (float) $plan->price : 0;
            $isDowngrade = $newPrice < $oldPrice || ($oldPrice > 0 && $newPrice === 0.0);

            // Сохраняем ends_at только при понижении (дешевле или переход на бесплатный)
            if ($isDowngrade && $subscription->ends_at && $subscription->ends_at->isFuture()) {
                $preserveEndsAt = true;
                $endsAt = $subscription->ends_at;
            }
            // При повышении (free → платный или дешевле → дороже) ends_at уже задан выше (now + interval)
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
            $subscriptionData['payment_status'] = $invoice->isPaid()
                ? 'paid'
                : 'pending';
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

        // Проверяем изменение тарифа (при активации триала не шлём «тариф изменён» — отправим одно сообщение «начат пробный период»)
        if ($subscription && $oldPlan && $oldPlan->id !== $plan->id && ! $isTrial) {
            \Illuminate\Support\Facades\Log::info('Subscription plan changed', [
                'channel' => 'subscription',
                'event' => 'subscription_plan_changed',
                'subscription_id' => $subscription->id,
                'old_plan_id' => $oldPlan->id,
                'new_plan_id' => $plan->id,
            ]);
            \App\Services\SubscriptionNotificationService::notifyPlanChanged(
                $subscription,
                $oldPlan,
                $plan,
            );
        }

        // Проверяем начало пробного периода (одно сообщение в TG/email/in-app)
        if ($isTrial && $trialEndsAt !== null) {
            $subscription->setRelation('plan', $plan);
            \App\Services\SubscriptionNotificationService::notifyTrialStarted(
                $subscription,
            );
        }

        return $subscription;
    }

    /**
     * Инициализировать usage для всех метрик тарифа (период от даты начала подписки).
     */
    protected function initializeUsage(Subscription $subscription): void
    {
        $period = $this->getCurrentPeriodForSubscription($subscription);
        $periodStart = $period['period_start'];
        $periodEnd = $period['period_end'];

        $features = $subscription->plan->features()->with('metric')->get();

        foreach ($features as $feature) {
            $metric = $feature->metric;
            if (! $metric) {
                continue;
            }
            if (
                $metric->type === 'integer' &&
                $this->isMonthlyMetric($metric->key)
            ) {
                SubscriptionUsage::firstOrCreate(
                    [
                        'subscription_id' => $subscription->id,
                        'user_id' => $subscription->user_id,
                        'feature_key' => $metric->key,
                        'period_start' => $periodStart,
                    ],
                    [
                        'current_usage' => 0,
                        'period_end' => $periodEnd,
                    ],
                );
            }
        }
    }

    /**
     * Проверить лимит для метрики (можно ли создавать новое)
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
     * Проверить, превышен ли лимит по метрике (для показа предупреждений в UI).
     * true только при активной подписке и когда текущее использование >= лимита (лимит не безлимит).
     * Когда true — создание нового заблокировано, существующие данные доступны для просмотра/редактирования.
     */
    public function isOverLimit(User $user, string $featureKey): bool
    {
        $subscription = $user->activeSubscription();
        if (! $subscription) {
            return false;
        }
        $limit = $this->getLimit($user, $featureKey);
        if ($limit === null || $limit === -1 || $limit === true) {
            return false;
        }
        $currentUsage = $this->getCurrentUsage($user, $featureKey);

        return $currentUsage >= $limit;
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

        // Для месячных метрик — период от даты начала подписки
        if ($this->isMonthlyMetric($featureKey)) {
            $period = $this->getCurrentPeriodForSubscription($subscription);
            $usage = SubscriptionUsage::where('user_id', $user->id)
                ->where('feature_key', $featureKey)
                ->where('period_start', $period['period_start'])
                ->first();

            return $usage ? $usage->current_usage : 0;
        }

        // Для остальных метрик получаем данные напрямую
        return match ($featureKey) {
            'max_locations' => $user
                ->businesses()
                ->withCount('locations')
                ->get()
                ->sum('locations_count'),
            'max_masters' => $user
                ->businesses()
                ->withCount('masters')
                ->get()
                ->sum('masters_count'),
            'max_services' => $user
                ->businesses()
                ->withCount('services')
                ->get()
                ->sum('services_count'),
            'max_clients' => $user
                ->businesses()
                ->withCount('clients')
                ->get()
                ->sum('clients_count'),
            'max_business_users' => $this->getBusinessUsersCount($user),
            'max_businesses' => Business::where('owner_id', $user->id)->count(),
            default => 0,
        };
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
    public function getMultipleUsageAndLimits(
        User $user,
        array $featureKeys,
    ): array {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return array_fill_keys($featureKeys, [
                'current' => 0,
                'limit' => 0,
                'percentage' => 0,
                'warning' => false,
            ]);
        }

        // Получаем все usage для месячных метрик одним запросом (период от starts_at)
        $monthlyKeys = array_filter(
            $featureKeys,
            fn ($key) => $this->isMonthlyMetric($key),
        );

        $usageData = [];
        if (! empty($monthlyKeys)) {
            $period = $this->getCurrentPeriodForSubscription($subscription);
            $usages = SubscriptionUsage::where('user_id', $user->id)
                ->whereIn('feature_key', $monthlyKeys)
                ->where('period_start', $period['period_start'])
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
                // «Приближается к лимиту» только когда >= 80% и ещё не достигнут (не включая 100%)
                'warning' => $limit > 0 && $current < $limit && $current / $limit >= 0.8,
            ];
        }

        return $result;
    }

    /**
     * Увеличить использование метрики
     */
    public function incrementUsage(
        User $user,
        string $featureKey,
        int $amount = 1,
    ): void {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return;
        }

        // Для месячных метрик — период от даты начала подписки
        if ($this->isMonthlyMetric($featureKey)) {
            $period = $this->getCurrentPeriodForSubscription($subscription);

            $usage = SubscriptionUsage::firstOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'feature_key' => $featureKey,
                    'period_start' => $period['period_start'],
                ],
                [
                    'current_usage' => 0,
                    'period_end' => $period['period_end'],
                ],
            );

            $usage->increment('current_usage', $amount);
        }
    }

    /**
     * Уменьшить использование метрики
     */
    public function decrementUsage(
        User $user,
        string $featureKey,
        int $amount = 1,
    ): void {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return;
        }

        // Для месячных метрик — период от даты начала подписки
        if ($this->isMonthlyMetric($featureKey)) {
            $period = $this->getCurrentPeriodForSubscription($subscription);
            $usage = SubscriptionUsage::where('user_id', $user->id)
                ->where('feature_key', $featureKey)
                ->where('period_start', $period['period_start'])
                ->first();

            if ($usage) {
                $usage->decrement('current_usage', $amount);
                if ($usage->current_usage < 0) {
                    $usage->update(['current_usage' => 0]);
                }
            }
        }
    }

    /**
     * Обеспечить наличие записей usage для текущего периода (от starts_at) и удалить старые.
     * Обнуление по календарю не используется — период у каждого от даты начала подписки.
     */
    public function resetMonthlyUsage(User $user): void
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return;
        }

        $period = $this->getCurrentPeriodForSubscription($subscription);
        $periodStart = $period['period_start'];
        $periodEnd = $period['period_end'];

        // Удаляем старые записи (периоды, которые уже закончились)
        SubscriptionUsage::where('user_id', $user->id)
            ->where('period_end', '<', $periodStart)
            ->where(function ($query) {
                $query
                    ->where('feature_key', 'max_appointments_per_month')
                    ->orWhere('feature_key', 'like', '%_per_month')
                    ->orWhere('feature_key', 'like', '%_monthly');
            })
            ->delete();

        // Создаём записи для текущего периода, если ещё нет
        $features = $subscription->plan->features()->with('metric')->get();
        foreach ($features as $feature) {
            $metric = $feature->metric;
            if (! $metric || $metric->type !== 'integer' || ! $this->isMonthlyMetric($metric->key)) {
                continue;
            }
            SubscriptionUsage::firstOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'feature_key' => $metric->key,
                    'period_start' => $periodStart,
                ],
                [
                    'current_usage' => 0,
                    'period_end' => $periodEnd,
                ],
            );
        }
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
     * Проверить возможность создания нового бизнеса
     */
    public function canCreateBusiness(User $user): bool
    {
        return $this->checkLimit($user, 'max_businesses');
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
            $usersCount = $business
                ->users()
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
        return str_contains($featureKey, '_per_month') ||
            str_contains($featureKey, '_monthly');
    }

    /**
     * Текущий период использования для подписки (от даты начала подписки, не календарный месяц).
     * Возвращает [period_start, period_end] — период, в который попадает now().
     */
    public function getCurrentPeriodForSubscription(Subscription $subscription): array
    {
        $anchor = $subscription->starts_at->copy()->startOfDay();
        $now = now();
        if ($now->lt($anchor)) {
            return [
                'period_start' => $anchor,
                'period_end' => $anchor->copy()->addMonth()->subDay(),
            ];
        }
        $monthsSinceStart = (int) $anchor->diffInMonths($now);
        $periodStart = $anchor->copy()->addMonths($monthsSinceStart);
        $periodEnd = $periodStart->copy()->addMonth()->subDay();

        return [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ];
    }

    /**
     * Проверить, использовал ли пользователь пробный период для конкретного тарифа
     */
    /**
     * Проверить использование trial для нескольких планов сразу (оптимизация N+1)
     *
     * @return array<int, bool> plan_id => used
     */
    public function hasUsedTrialForPlans(User $user, \Illuminate\Support\Collection $plans): array
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
            if (
                $subscription->plan_id === $plan->id &&
                $subscription->trial_ends_at !== null &&
                $subscription->trial_ends_at->isPast()
            ) {
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
        if (
            $subscription->plan_id === $plan->id &&
            $subscription->trial_ends_at !== null
        ) {
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

    /**
     * Админ: изменить статус подписки
     */
    public function adminUpdateStatus(Subscription $subscription, string $status): void
    {
        $allowed = ['trial', 'active', 'past_due', 'cancelled', 'expired'];
        if (! in_array($status, $allowed, true)) {
            return;
        }
        $data = ['status' => $status];
        if ($status === 'cancelled' && $subscription->cancelled_at === null) {
            $data['cancelled_at'] = now();
        }
        if (in_array($status, ['active', 'trial'], true)) {
            $data['cancelled_at'] = null;
        }
        $subscription->update($data);
        $subscription->user?->clearSubscriptionCache();
    }

    /**
     * Админ: продлить подписку до указанной даты
     */
    public function adminExtend(Subscription $subscription, \DateTimeInterface|string $endsAt): void
    {
        $date = $endsAt instanceof \DateTimeInterface
            ? \Carbon\Carbon::instance($endsAt)
            : \Carbon\Carbon::parse($endsAt);
        $subscription->update(['ends_at' => $date]);
        if ($subscription->status === 'expired') {
            $subscription->update(['status' => 'active', 'cancelled_at' => null]);
        }
        $subscription->user?->clearSubscriptionCache();
    }

    /**
     * Админ: отменить подписку в конце периода (установить cancelled_at, продление не будет).
     */
    public function adminCancelAtEnd(Subscription $subscription): void
    {
        if ($subscription->cancelled_at !== null) {
            return;
        }
        $subscription->update(['cancelled_at' => now()]);
        $subscription->user?->clearSubscriptionCache();
    }

    /**
     * Админ: выдать подписку пользователю на любой тариф и срок.
     * Если у пользователя уже есть подписка — она обновляется (план, даты, статус).
     *
     * @param  int|null  $days  количество дней (если null и plan не free — по умолчанию 1 месяц)
     */
    public function adminGrant(User $user, Plan $plan, ?\DateTimeInterface $endsAt = null, bool $asTrial = false, ?int $days = null): Subscription
    {
        $now = now();
        $trialEndsAt = null;
        // Явно указанные дата или кол-во дней имеют приоритет (в т.ч. для триала)
        if ($days !== null) {
            $calculatedEndsAt = $now->copy()->addDays($days);
        } else {
            $calculatedEndsAt = $endsAt;
        }

        if ($asTrial) {
            if ($calculatedEndsAt !== null) {
                $trialEndsAt = $calculatedEndsAt;
            } elseif ($plan->trial_days > 0) {
                $trialEndsAt = $now->copy()->addDays($plan->trial_days);
                $calculatedEndsAt = $trialEndsAt;
            }
        } elseif ($calculatedEndsAt === null && $plan->slug !== 'free') {
            $calculatedEndsAt = $plan->interval === 'yearly'
                ? $now->copy()->addYear()
                : $now->copy()->addMonth();
        }

        $status = $asTrial ? 'trial' : 'active';
        $subscription = $user->subscription()->first();

        $metadata = $subscription?->metadata ?? [];
        if ($asTrial && $plan->trial_days > 0) {
            $usedTrials = $metadata['used_trials'] ?? [];
            if (! in_array($plan->id, $usedTrials)) {
                $usedTrials[] = $plan->id;
                $metadata['used_trials'] = $usedTrials;
            }
        }

        $payload = [
            'plan_id' => $plan->id,
            'status' => $status,
            'starts_at' => $now,
            'ends_at' => $calculatedEndsAt,
            'trial_ends_at' => $trialEndsAt,
            'cancelled_at' => null,
            'metadata' => $metadata,
        ];

        if ($subscription) {
            $subscription->update($payload);
            $sub = $subscription->fresh();
        } else {
            $sub = Subscription::create(array_merge($payload, ['user_id' => $user->id]));
        }

        $this->initializeUsage($sub);
        $user->clearSubscriptionCache();

        return $sub;
    }
}
