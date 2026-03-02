<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\GatewayManager;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ExpressPayWebhookController extends Controller
{
    public function __construct(
        protected GatewayManager $gatewayManager,
        protected SubscriptionService $subscriptionService,
    ) {}

    /**
     * Обработка webhook от Express Pay
     */
    public function handle(Request $request): Response
    {
        Log::info('[ExpressPay Webhook] Received', [
            'data' => $request->input('Data'),
            'signature' => $request->input('Signature'),
            'ip' => $request->ip(),
        ]);

        try {
            $gateway = $this->gatewayManager->get('expresspay');

            // Валидация подписи
            if (! $gateway->validateWebhook($request)) {
                Log::warning('[ExpressPay Webhook] Invalid signature');

                return response('Invalid signature', 403);
            }

            // Парсим данные
            $webhookData = $gateway->parseWebhook($request);

            Log::info('[ExpressPay Webhook] Parsed', [
                'status' => $webhookData->status,
                'order_id' => $webhookData->orderId,
                'transaction_id' => $webhookData->transactionId,
                'amount' => $webhookData->amount,
            ]);

            // Ищем инвойс по AccountNo (это наш invoice_id)
            $invoice = Invoice::find($webhookData->orderId);

            if (! $invoice) {
                Log::warning('[ExpressPay Webhook] Invoice not found', [
                    'order_id' => $webhookData->orderId,
                ]);

                // Возвращаем 200, чтобы Express Pay не повторял запрос
                return response('Invoice not found', 200);
            }

            // Проверяем, не обработан ли уже платёж
            if ($invoice->isPaid() && $webhookData->status === 'successful') {
                Log::info('[ExpressPay Webhook] Invoice already paid', [
                    'invoice_id' => $invoice->id,
                ]);

                return response('OK', 200);
            }

            // Обрабатываем в зависимости от статуса
            match ($webhookData->status) {
                'successful' => $this->handleSuccessfulPayment($invoice, $webhookData),
                'failed' => $this->handleFailedPayment($invoice, $webhookData),
                'refunded' => $this->handleRefundedPayment($invoice, $webhookData),
                default => Log::info('[ExpressPay Webhook] Unhandled status', [
                    'status' => $webhookData->status,
                    'invoice_id' => $invoice->id,
                ]),
            };

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('[ExpressPay Webhook] Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Возвращаем 500, чтобы Express Pay повторил запрос
            return response('Internal error', 500);
        }
    }

    /**
     * Обработка успешного платежа
     */
    protected function handleSuccessfulPayment(Invoice $invoice, $webhookData): void
    {
        Log::info('[ExpressPay Webhook] Processing successful payment', [
            'invoice_id' => $invoice->id,
            'transaction_id' => $webhookData->transactionId,
        ]);

        // Обновляем инвойс
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_transaction_id' => $webhookData->transactionId,
        ]);

        // Активируем подписку
        if ($invoice->payment_type === 'subscription') {
            if ($invoice->subscription_id) {
                $subscription = $invoice->subscription;
                $subscription->update([
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'invoice_id' => $invoice->id,
                ]);

                Log::info('[ExpressPay Webhook] Subscription activated', [
                    'invoice_id' => $invoice->id,
                    'subscription_id' => $subscription->id,
                ]);
            } else {
                // Создаём подписку
                $subscription = $this->subscriptionService->createSubscription(
                    $invoice->user,
                    $invoice->plan,
                    false,
                    $invoice
                );

                Log::info('[ExpressPay Webhook] Subscription created', [
                    'invoice_id' => $invoice->id,
                    'subscription_id' => $subscription->id,
                ]);
            }
        }
    }

    /**
     * Обработка неуспешного платежа
     */
    protected function handleFailedPayment(Invoice $invoice, $webhookData): void
    {
        Log::info('[ExpressPay Webhook] Processing failed payment', [
            'invoice_id' => $invoice->id,
        ]);

        if ($invoice->isPending()) {
            $invoice->update(['status' => 'failed']);
        }
    }

    /**
     * Обработка возврата
     */
    protected function handleRefundedPayment(Invoice $invoice, $webhookData): void
    {
        Log::info('[ExpressPay Webhook] Processing refund', [
            'invoice_id' => $invoice->id,
        ]);

        $invoice->update(['status' => 'refunded']);

        // Деактивируем подписку если есть
        if ($invoice->subscription_id) {
            $invoice->subscription->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]);
        }
    }
}
