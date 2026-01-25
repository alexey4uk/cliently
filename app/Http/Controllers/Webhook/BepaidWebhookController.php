<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\BepaidService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BepaidWebhookController extends Controller
{
    protected BepaidService $bepaidService;
    protected SubscriptionService $subscriptionService;

    public function __construct(BepaidService $bepaidService, SubscriptionService $subscriptionService)
    {
        $this->bepaidService = $bepaidService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Обработка webhook от bePaid
     * 
     * bePaid отправляет webhook с HTTP Basic Auth
     * Данные приходят в формате JSON
     */
    public function handle(Request $request)
    {
        try {
            // Логируем входящий запрос для отладки
            if (config('bepaid.logging.enabled')) {
                Log::info('bePaid webhook received', [
                    'headers' => $request->headers->all(),
                    'body' => $request->all(),
                ]);
            }

            // Получаем данные из запроса
            $data = $request->all();

            if (empty($data)) {
                // Пытаемся получить из JSON
                $data = json_decode($request->getContent(), true);
            }

            if (empty($data)) {
                Log::warning('bePaid webhook: empty data');
                return response()->json(['error' => 'Empty data'], 400);
            }

            // Валидация данных через сервис
            if (! $this->bepaidService->validateWebhookRequest($data)) {
                Log::warning('bePaid webhook: invalid data', ['data' => $data]);
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Обрабатываем webhook через сервис
            $invoice = $this->bepaidService->processWebhook($data);

            if (! $invoice) {
                Log::warning('bePaid webhook: invoice not found');
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Если платеж успешен, активируем/продлеваем подписку
            if ($invoice->isPaid()) {
                $this->activateSubscription($invoice);
            }

            // Возвращаем успешный ответ
            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error('bePaid webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            // Возвращаем ошибку, но с кодом 200, чтобы bePaid не повторял запрос
            // В реальности можно настроить повторные попытки
            return response()->json(['error' => 'Processing error'], 200);
        }
    }

    /**
     * Активировать или продлить подписку после успешной оплаты
     */
    protected function activateSubscription(Invoice $invoice): void
    {
        $subscription = $invoice->subscription;
        $plan = $invoice->plan;
        $user = $invoice->user;

        // Если подписки нет, создаем её
        if (! $subscription) {
            $subscription = $this->subscriptionService->createSubscription($user, $plan, false, $invoice);
            Log::info('Subscription created after payment', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'plan_id' => $plan->id,
            ]);
        } else {
            // Обновляем существующую подписку
            $now = now();
            $endsAt = null;

            if ($plan->interval === 'monthly') {
                $endsAt = $now->copy()->addMonth();
            } elseif ($plan->interval === 'yearly') {
                $endsAt = $now->copy()->addYear();
            }

            $subscription->update([
                'status' => 'active',
                'starts_at' => $now,
                'ends_at' => $endsAt,
                'payment_status' => 'paid',
                'invoice_id' => $invoice->id,
                'cancelled_at' => null, // Снимаем отмену, если была
            ]);

            Log::info('Subscription activated after payment', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'plan_id' => $plan->id,
            ]);
        }
    }
}
