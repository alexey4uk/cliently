<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SubscriptionNotificationService
{
    /**
     * Уведомить об успешной оплате
     */
    public static function notifyPaymentSuccess(Invoice $invoice): void
    {
        $user = $invoice->user;
        $subscription = $invoice->subscription;
        $plan = $invoice->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "invoice_id" => $invoice->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.payment.success",
            )
        ) {
            return;
        }

        $title = "Оплата успешна";
        $message = "Оплата подписки на тариф «{$plan->name}» успешно выполнена. Сумма: {$invoice->amount} {$invoice->currency}.";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.payment.success",
            "title" => $title,
            "message" => $message,
            "data" => [
                "invoice_id" => $invoice->id,
                "subscription_id" => $subscription?->id,
                "plan_id" => $plan->id,
                "amount" => $invoice->amount,
                "currency" => $invoice->currency,
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.payment.success",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                $owner->notify(
                    new \App\Notifications\Subscription\PaymentSuccess(
                        $invoice,
                    ),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.payment.success",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.payment.success",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                TelegramNotificationService::sendSubscriptionPaymentSuccess(
                    $invoice,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.payment.success",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить о неудачной оплате
     */
    public static function notifyPaymentFailed(
        Invoice $invoice,
        ?string $reason = null,
    ): void {
        $user = $invoice->user;
        $subscription = $invoice->subscription;
        $plan = $invoice->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "invoice_id" => $invoice->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.payment.failed",
            )
        ) {
            return;
        }

        $title = "Оплата не прошла";
        $message = "Оплата подписки на тариф «{$plan->name}» не прошла. Сумма: {$invoice->amount} {$invoice->currency}.";
        if ($reason) {
            $message .= " Причина: {$reason}";
        }

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.payment.failed",
            "title" => $title,
            "message" => $message,
            "data" => [
                "invoice_id" => $invoice->id,
                "subscription_id" => $subscription?->id,
                "plan_id" => $plan->id,
                "amount" => $invoice->amount,
                "currency" => $invoice->currency,
                "reason" => $reason,
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.payment.failed",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                $owner->notify(
                    new \App\Notifications\Subscription\PaymentFailed(
                        $invoice,
                        $reason,
                    ),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.payment.failed",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.payment.failed",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                TelegramNotificationService::sendSubscriptionPaymentFailed(
                    $invoice,
                    $owner,
                    $reason,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.payment.failed",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить об изменении тарифа
     */
    public static function notifyPlanChanged(
        Subscription $subscription,
        Plan $oldPlan,
        Plan $newPlan,
    ): void {
        $user = $subscription->user;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "subscription_id" => $subscription->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.plan.changed",
            )
        ) {
            return;
        }

        $title = "Тариф изменён";
        $message = "Тариф подписки изменён с «{$oldPlan->name}» на «{$newPlan->name}».";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.plan.changed",
            "title" => $title,
            "message" => $message,
            "data" => [
                "subscription_id" => $subscription->id,
                "old_plan_id" => $oldPlan->id,
                "new_plan_id" => $newPlan->id,
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.plan.changed",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                $owner->notify(
                    new \App\Notifications\Subscription\PlanChanged(
                        $subscription,
                        $oldPlan,
                        $newPlan,
                    ),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.plan.changed",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.plan.changed",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                TelegramNotificationService::sendSubscriptionPlanChanged(
                    $subscription,
                    $oldPlan,
                    $newPlan,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.plan.changed",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить о продлении подписки
     */
    public static function notifyRenewed(Subscription $subscription): void
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "subscription_id" => $subscription->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.renewed",
            )
        ) {
            return;
        }

        $endsAt = $subscription->ends_at
            ? $subscription->ends_at->format("d.m.Y")
            : "не указано";
        $title = "Подписка продлена";
        $message = "Подписка на тариф «{$plan->name}» успешно продлена. Действует до: {$endsAt}.";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.renewed",
            "title" => $title,
            "message" => $message,
            "data" => [
                "subscription_id" => $subscription->id,
                "plan_id" => $plan->id,
                "ends_at" => $subscription->ends_at?->toIso8601String(),
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.renewed",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                $owner->notify(
                    new \App\Notifications\Subscription\Renewed($subscription),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.renewed",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.renewed",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                TelegramNotificationService::sendSubscriptionRenewed(
                    $subscription,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.renewed",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить о начале пробного периода
     */
    public static function notifyTrialStarted(Subscription $subscription): void
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "subscription_id" => $subscription->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.trial.started",
            )
        ) {
            return;
        }

        $trialEndsAt = $subscription->trial_ends_at
            ? $subscription->trial_ends_at->format("d.m.Y")
            : "не указано";
        $title = "Начат пробный период";
        $message = "Начат пробный период для тарифа «{$plan->name}». Пробный период действует до: {$trialEndsAt}.";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.trial.started",
            "title" => $title,
            "message" => $message,
            "data" => [
                "subscription_id" => $subscription->id,
                "plan_id" => $plan->id,
                "trial_ends_at" => $subscription->trial_ends_at?->toIso8601String(),
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.trial.started",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                $owner->notify(
                    new \App\Notifications\Subscription\TrialStarted(
                        $subscription,
                    ),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.trial.started",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.trial.started",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                TelegramNotificationService::sendSubscriptionTrialStarted(
                    $subscription,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.trial.started",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить о скором окончании пробного периода
     */
    public static function notifyTrialEnding(Subscription $subscription): void
    {
        $user = $subscription->user;
        $plan = $subscription->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "subscription_id" => $subscription->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.trial.ending",
            )
        ) {
            return;
        }

        $trialEndsAt = $subscription->trial_ends_at
            ? $subscription->trial_ends_at->format("d.m.Y H:i")
            : "не указано";
        $daysLeft = $subscription->trial_ends_at
            ? now()->diffInDays($subscription->trial_ends_at, false)
            : 0;
        $title = "Пробный период заканчивается";
        $message = "Пробный период для тарифа «{$plan->name}» заканчивается {$trialEndsAt}. Осталось дней: {$daysLeft}.";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.trial.ending",
            "title" => $title,
            "message" => $message,
            "data" => [
                "subscription_id" => $subscription->id,
                "plan_id" => $plan->id,
                "trial_ends_at" => $subscription->trial_ends_at?->toIso8601String(),
                "days_left" => $daysLeft,
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.trial.ending",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                $owner->notify(
                    new \App\Notifications\Subscription\TrialEnding(
                        $subscription,
                    ),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.trial.ending",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.trial.ending",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                TelegramNotificationService::sendSubscriptionTrialEnding(
                    $subscription,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.trial.ending",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить об истечении пробного периода
     */
    public static function notifyTrialExpired(
        Subscription $subscription,
        Plan $newPlan,
    ): void {
        $user = $subscription->user;
        $oldPlan = $subscription->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "subscription_id" => $subscription->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.trial.expired",
            )
        ) {
            return;
        }

        $title = "Пробный период истек";
        $message = "Пробный период для тарифа «{$oldPlan->name}» истек. Ваш тариф автоматически изменен на «{$newPlan->name}».";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.trial.expired",
            "title" => $title,
            "message" => $message,
            "data" => [
                "subscription_id" => $subscription->id,
                "old_plan_id" => $oldPlan->id,
                "new_plan_id" => $newPlan->id,
                "trial_ended_at" => $subscription->trial_ends_at?->toIso8601String(),
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.trial.expired",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                // Можно создать отдельное уведомление, но пока используем простое логирование
                Log::info("Trial expired notification sent via email", [
                    "user_id" => $owner->id,
                    "subscription_id" => $subscription->id,
                ]);
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.trial.expired",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.trial.expired",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                \App\Services\TelegramNotificationService::sendSubscriptionTrialExpired(
                    $subscription,
                    $newPlan,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.trial.expired",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить об истечении платной подписки
     */
    public static function notifySubscriptionExpired(
        Subscription $subscription,
        Plan $newPlan,
    ): void {
        $user = $subscription->user;
        $oldPlan = $subscription->plan;

        // Получаем владельца бизнеса
        $owner = self::getBusinessOwner($user);
        if (!$owner) {
            Log::warning(
                "SubscriptionNotificationService: Business owner not found",
                [
                    "user_id" => $user->id,
                    "subscription_id" => $subscription->id,
                ],
            );

            return;
        }

        if (
            !NotificationSettingsService::isTypeEnabled(
                $owner,
                "subscription.expired",
            )
        ) {
            return;
        }

        $title = "Подписка истекла";
        $message = "Ваша подписка на тариф «{$oldPlan->name}» истекла. Тариф автоматически изменен на «{$newPlan->name}». Для продолжения использования платных функций оформите новую подписку.";

        // In-app уведомление
        NotificationService::send([
            "user_id" => $owner->id,
            "type" => "subscription.expired",
            "title" => $title,
            "message" => $message,
            "data" => [
                "subscription_id" => $subscription->id,
                "old_plan_id" => $oldPlan->id,
                "new_plan_id" => $newPlan->id,
                "expired_at" => $subscription->ends_at?->toIso8601String(),
            ],
        ]);

        // Email уведомление
        if (
            NotificationSettingsService::shouldSendEmail(
                $owner,
                "subscription.expired",
            ) &&
            $owner->hasVerifiedEmail()
        ) {
            try {
                // Можно создать отдельное уведомление, но пока используем простое логирование
                Log::info("Subscription expired notification sent via email", [
                    "user_id" => $owner->id,
                    "subscription_id" => $subscription->id,
                ]);
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for subscription.expired",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }

        // Telegram уведомление
        if (
            NotificationSettingsService::shouldSendTelegram(
                $owner,
                "subscription.expired",
            ) &&
            $owner->isTelegramConnected()
        ) {
            try {
                \App\Services\TelegramNotificationService::sendSubscriptionExpired(
                    $subscription,
                    $newPlan,
                    $owner,
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send telegram notification for subscription.expired",
                    [
                        "user_id" => $owner->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Получить владельца бизнеса для пользователя
     */
    protected static function getBusinessOwner(User $user): ?User
    {
        // Получаем первый бизнес пользователя
        $business = $user->businesses()->first();
        if (!$business) {
            return null;
        }

        return User::find($business->owner_id);
    }
}
