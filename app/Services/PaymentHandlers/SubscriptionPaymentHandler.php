<?php

declare(strict_types=1);

namespace App\Services\PaymentHandlers;

use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionNotificationService;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Обработчик оплаты подписок
 */
class SubscriptionPaymentHandler extends AbstractPaymentHandler
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function createInvoice(User $user, array $data): Invoice
    {
        $plan = Plan::findOrFail($data['plan_id']);
        $subscription = isset($data['subscription_id'])
            ? Subscription::find($data['subscription_id'])
            : $user->subscription;

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription?->id,
            'amount' => $plan->price,
            'currency' => $data['currency'] ?? config('payments.default_currency', 'BYN'),
            'status' => 'pending',
            'payment_type' => 'subscription',
            'payment_method' => $data['payment_method'] ?? 'redirect',
            'metadata' => [
                'is_renewal' => $data['is_renewal'] ?? false,
                'preserve_ends_at' => $data['preserve_ends_at'] ?? false,
            ],
        ]);

        $this->log('Invoice created', [
            'invoice_id' => $invoice->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
        ]);

        return $invoice;
    }

    /**
     * {@inheritdoc}
     */
    public function onPaymentSuccess(Invoice $invoice, WebhookData $webhookData): void
    {
        $subscription = $invoice->subscription;
        $plan = $invoice->plan;
        $user = $invoice->user;

        // Сохраняем старые значения для проверки продления
        $oldEndsAt = $subscription?->ends_at;

        // Идемпотентность: если подписка уже активна и оплачена этим инвойсом
        if (
            $subscription &&
            $subscription->invoice_id === $invoice->id &&
            $subscription->status === 'active'
        ) {
            $this->log('Subscription already activated for this invoice', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
            ]);

            return;
        }

        // Если подписки нет, создаем её
        if (! $subscription) {
            $subscription = $this->subscriptionService->createSubscription(
                $user,
                $plan,
                false,
                $invoice,
            );

            $this->log('Subscription created after payment', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'plan_id' => $plan->id,
            ]);

            SubscriptionNotificationService::notifyPaymentSuccess($invoice);
        } else {
            // Обновляем существующую подписку
            $this->activateSubscription($subscription, $invoice, $plan, $oldEndsAt);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onPaymentFailed(Invoice $invoice, WebhookData $webhookData): void
    {
        $invoice->update(['status' => 'failed']);

        SubscriptionNotificationService::notifyPaymentFailed($invoice);

        $this->log('Payment failed', [
            'invoice_id' => $invoice->id,
            'message' => $webhookData->message,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function onPaymentRefunded(Invoice $invoice): void
    {
        parent::onPaymentRefunded($invoice);

        // Можно добавить логику деактивации подписки при возврате
        // Пока просто логируем
        $this->log('Payment refunded', [
            'invoice_id' => $invoice->id,
            'subscription_id' => $invoice->subscription_id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentDescription(Invoice $invoice): string
    {
        $plan = $invoice->plan;

        if ($plan) {
            return "Оплата подписки: {$plan->name}";
        }

        return "Оплата подписки #{$invoice->id}";
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessRedirectUrl(Invoice $invoice): string
    {
        return route('subscription.index', ['payment' => 'success']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailRedirectUrl(Invoice $invoice): string
    {
        return route('subscription.index', ['payment' => 'failed']);
    }

    /**
     * {@inheritdoc}
     */
    public function validateData(array $data): array
    {
        $validator = Validator::make($data, [
            'plan_id' => 'required|exists:plans,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'currency' => 'nullable|string|size:3',
            'payment_method' => 'nullable|in:redirect,widget',
            'is_renewal' => 'nullable|boolean',
            'preserve_ends_at' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Активировать или продлить подписку
     */
    protected function activateSubscription(
        Subscription $subscription,
        Invoice $invoice,
        Plan $plan,
        ?\Carbon\Carbon $oldEndsAt
    ): void {
        $now = now();
        $endsAt = null;
        $oldPlan = $subscription->plan;
        $isPlanChange = $oldPlan && $oldPlan->id !== $plan->id;

        // Проверяем, является ли это продлением
        $isRenewal = $invoice->metadata['is_renewal'] ?? false;

        // Проверяем, нужно ли сохранять ends_at при смене тарифа
        $preserveEndsAt = $invoice->metadata['preserve_ends_at'] ?? false;

        // Получаем текущий metadata
        $metadata = $subscription->metadata ?? [];

        // Если это смена тарифа и нужно сохранить ends_at
        if (
            $isPlanChange &&
            $preserveEndsAt &&
            $subscription->ends_at &&
            $subscription->ends_at->isFuture()
        ) {
            $endsAt = $subscription->ends_at;

            $metadata['previous_plan_id'] = $oldPlan->id;
            $metadata['previous_plan_name'] = $oldPlan->name;
            $metadata['preserved_ends_at'] = $subscription->ends_at->toIso8601String();
        } elseif (
            $isRenewal &&
            $subscription->ends_at &&
            $subscription->ends_at->isFuture()
        ) {
            // Продление: продлеваем от текущего ends_at
            $baseDate = $subscription->ends_at;
            $endsAt = $this->calculateEndsAt($baseDate, $plan);

            unset($metadata['previous_plan_id']);
            unset($metadata['previous_plan_name']);
            unset($metadata['preserved_ends_at']);
        } else {
            // Новая подписка или истекшая
            $endsAt = $this->calculateEndsAt($now, $plan);

            unset($metadata['previous_plan_id']);
            unset($metadata['previous_plan_name']);
            unset($metadata['preserved_ends_at']);
        }

        $subscription->update([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $now,
            'ends_at' => $endsAt,
            'payment_status' => 'paid',
            'invoice_id' => $invoice->id,
            'cancelled_at' => null,
            'metadata' => $metadata,
        ]);

        $this->log('Subscription activated after payment', [
            'subscription_id' => $subscription->id,
            'invoice_id' => $invoice->id,
            'plan_id' => $plan->id,
        ]);

        // Уведомляем об успешной оплате
        SubscriptionNotificationService::notifyPaymentSuccess($invoice);

        // Проверяем, является ли это продлением
        if (
            $oldEndsAt &&
            $subscription->ends_at &&
            $oldEndsAt->lt($subscription->ends_at)
        ) {
            SubscriptionNotificationService::notifyRenewed($subscription);
        }
    }

    /**
     * Рассчитать дату окончания подписки
     */
    protected function calculateEndsAt(\Carbon\Carbon $baseDate, Plan $plan): \Carbon\Carbon
    {
        return match ($plan->interval) {
            'monthly' => $baseDate->copy()->addMonth(),
            'yearly' => $baseDate->copy()->addYear(),
            'weekly' => $baseDate->copy()->addWeek(),
            'daily' => $baseDate->copy()->addDay(),
            default => $baseDate->copy()->addMonth(),
        };
    }
}
