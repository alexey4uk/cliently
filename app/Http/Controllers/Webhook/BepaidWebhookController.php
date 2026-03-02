<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\GatewayManager;
use App\Services\Gateways\BepaidGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BepaidWebhookController extends Controller
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
     * Обработка webhook от bePaid
     *
     * bePaid отправляет webhook с HTTP Basic Auth
     * Данные приходят в формате JSON
     */
    public function handle(Request $request)
    {
        try {
            // Логируем входящий запрос
            if (config('payments.logging') || config('payments.gateways.bepaid.logging')) {
                Log::info('bePaid webhook received', [
                    'headers' => $request->headers->all(),
                    'body' => $request->all(),
                ]);
            }

            /** @var BepaidGateway $gateway */
            $gateway = $this->gatewayManager->get('bepaid');

            // Проверка HTTP Basic Auth
            if (! $gateway->validateWebhook($request)) {
                Log::warning('bePaid webhook: invalid Basic Auth credentials');

                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Парсим данные webhook
            $webhookData = $gateway->parseWebhook($request);

            if (! $webhookData->orderId) {
                Log::warning('bePaid webhook: missing order ID');

                return response()->json(['error' => 'Missing order ID'], 400);
            }

            // Обрабатываем через PaymentService
            $invoice = $this->paymentService->processWebhook('bepaid', $webhookData);

            if (! $invoice) {
                Log::warning('bePaid webhook: invoice not found');

                return response()->json(['error' => 'Invoice not found'], 404);
            }

            return response()->json(['status' => 'ok'], 200);

        } catch (\RuntimeException $e) {
            Log::warning('bePaid webhook: validation error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);

        } catch (\Exception $e) {
            Log::error('bePaid webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            return response()->json(['error' => 'Processing error'], 500);
        }
    }
}
