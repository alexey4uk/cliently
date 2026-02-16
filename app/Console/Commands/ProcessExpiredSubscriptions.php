<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessExpiredSubscriptions extends Command
{
    protected $signature = 'subscription:process-expired';

    protected $description = 'Обработать истекшие платные подписки - перевести на бесплатный тариф или изменить статус';

    public function handle(): int
    {
        $this->info('Поиск истекших платных подписок...');

        // Находим подписки со статусом active, у которых ends_at уже в прошлом
        // Исключаем бесплатные тарифы (price === null)
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->whereHas('plan', function ($query) {
                $query->whereNotNull('price')
                    ->where('price', '>', 0);
            })
            ->with(['user', 'plan'])
            ->get();

        $this->info("Найдено истекших подписок: {$expiredSubscriptions->count()}");

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

        foreach ($expiredSubscriptions as $subscription) {
            try {
                $user = $subscription->user;

                if (! $user) {
                    $this->warn("Пользователь не найден для подписки ID: {$subscription->id}");

                    continue;
                }

                $oldPlan = $subscription->plan;

                // Переводим пользователя на бесплатный тариф
                $this->info("Обработка подписки ID: {$subscription->id}, пользователь: {$user->email}, тариф: {$oldPlan->name}");

                // Отправляем уведомление об истечении подписки (до изменения подписки)
                \App\Services\SubscriptionNotificationService::notifySubscriptionExpired($subscription, $freePlan);

                // Сохраняем ID старой подписки для логирования
                $oldSubscriptionId = $subscription->id;

                // Обновляем подписку на бесплатный тариф
                // createSubscription обновит существующую подписку, изменив план и статус
                $newSubscription = $subscriptionService->createSubscription($user, $freePlan, false);

                // Если подписка была обновлена (а не создана новая), помечаем её как expired для истории
                // Но только если это действительно была старая подписка
                if ($newSubscription->id === $oldSubscriptionId) {
                    // Подписка обновлена, но для истории можно сохранить информацию в metadata
                    $metadata = $newSubscription->metadata ?? [];
                    $metadata['previous_status'] = 'expired';
                    $metadata['expired_at'] = $subscription->ends_at?->toIso8601String();
                    $newSubscription->update(['metadata' => $metadata]);
                }

                $this->info("✓ Пользователь {$user->email} переведен на бесплатный тариф");

                $count++;

                // Логируем действие (id и статус, без персональных данных)
                Log::info('Subscription expired, user switched to free plan', [
                    'channel' => 'subscription',
                    'event' => 'subscription_expired',
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'old_plan_id' => $oldPlan->id,
                    'new_plan_id' => $freePlan->id,
                    'expired_at' => $subscription->ends_at?->toIso8601String(),
                ]);

            } catch (\Exception $e) {
                $this->error("Ошибка при обработке подписки ID: {$subscription->id}: {$e->getMessage()}");
                Log::error('Failed to process expired subscription', [
                    'channel' => 'subscription',
                    'event' => 'subscription_expired_error',
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
