<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\GatewayManager;
use App\Services\Gateways\FreekassaGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FreekassaWebhookController extends Controller
{
    protected PaymentService $paymentService;

    protected GatewayManager $gatewayManager;

    public function __construct(
        PaymentService $paymentService,
        GatewayManager $gatewayManager,
    ) {
        $this->paymentService = $paymentService;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Обработка webhook от FreeKassa
     *
     * FreeKassa отправляет уведомления только об успешных платежах
     * Данные приходят в формате POST
     */
    public function handle(Request $request)
    {
        try {
            // Логируем входящий запрос
            if (config('payments.logging') || config('payments.gateways.freekassa.logging')) {
                Log::info('FreeKassa webhook received', [
                    'ip' => $request->ip(),
                    'body' => $request->all(),
                ]);
            }

            /** @var FreekassaGateway $gateway */
            $gateway = $this->gatewayManager->get('freekassa');

            // Проверка IP адреса FreeKassa (опционально, но рекомендуется)
            if (! $gateway->isValidIp($request->ip())) {
                Log::warning('FreeKassa webhook: invalid IP address', [
                    'ip' => $request->ip(),
                ]);
                // Не блокируем, но логируем - IP может измениться
            }

            // Проверка подписи
            if (! $gateway->validateWebhook($request)) {
                Log::warning('FreeKassa webhook: invalid signature');

                return response('Invalid signature', 400);
            }

            // Парсим данные webhook
            $webhookData = $gateway->parseWebhook($request);

            if (! $webhookData->orderId) {
                Log::warning('FreeKassa webhook: missing order ID');

                return response('Missing order ID', 400);
            }

            // Обрабатываем через PaymentService
            $invoice = $this->paymentService->processWebhook('freekassa', $webhookData);

            if (! $invoice) {
                Log::warning('FreeKassa webhook: invoice not found');

                return response('Invoice not found', 404);
            }

            // FreeKassa ожидает ответ "YES" для подтверждения
            return response('YES', 200);

        } catch (\RuntimeException $e) {
            Log::warning('FreeKassa webhook: validation error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response($e->getMessage(), 400);

        } catch (\Exception $e) {
            Log::error('FreeKassa webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            return response('Processing error', 500);
        }
    }
}
