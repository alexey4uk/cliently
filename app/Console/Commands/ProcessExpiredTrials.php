<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessExpiredTrials extends Command
{
    protected $signature = 'subscription:process-expired-trials';

    protected $description = 'Обработать истекшие пробные периоды - перевести на бесплатный тариф или изменить статус';

    public function handle(): int
    {
        $this->info('Поиск истекших пробных периодов...');

        // Находим подписки со статусом trial, у которых trial_ends_at уже в прошлом
        $expiredTrials = Subscription::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now())
            ->with(['user', 'plan'])
            ->get();

        $this->info("Найдено истекших пробных периодов: {$expiredTrials->count()}");

        $count = 0;
        $subscriptionService = app(SubscriptionService::class);

        // Получаем бесплатный тариф
        $freePlan = Plan::where('slug', 'free')
            ->where('is_active', true)
            ->first();

        if (! $freePlan) {
            $this->error('Бесплатный тариф не найден! Создайте тариф со slug "free".');

            return self::FAILURE;
        }

        foreach ($expiredTrials as $subscription) {
            try {
                $user = $subscription->user;

                if (! $user) {
                    $this->warn("Пользователь не найден для подписки ID: {$subscription->id}");

                    continue;
                }

                // Переводим пользователя на бесплатный тариф
                $this->info("Обработка подписки ID: {$subscription->id}, пользователь: {$user->email}");

                // Сохраняем plan_id истекшего триала для логирования
                $oldPlanId = $subscription->plan_id;

                // Отправляем уведомление об истечении пробного периода
                \App\Services\SubscriptionNotificationService::notifyTrialExpired($subscription, $freePlan);

                // Обновляем подписку на бесплатный тариф
                // createSubscription сохранит существующий metadata (включая used_trials, который был добавлен при оформлении триала)
                $subscriptionService->createSubscription($user, $freePlan, false);

                $this->info("✓ Пользователь {$user->email} переведен на бесплатный тариф");

                $count++;

                // Логируем действие
                Log::info('Expired trial processed', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'old_plan_id' => $oldPlanId,
                    'new_plan_id' => $freePlan->id,
                    'trial_ended_at' => $subscription->trial_ends_at?->toIso8601String(),
                ]);

            } catch (\Exception $e) {
                $this->error("Ошибка при обработке подписки ID: {$subscription->id}: {$e->getMessage()}");
                Log::error('Failed to process expired trial', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info("Обработано подписок: {$count}");

        return self::SUCCESS;
    }
}
