<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Invoice;
use BeGateway\GetPaymentToken;
use BeGateway\QueryByUid;
use BeGateway\RefundOperation;
use BeGateway\Settings as BeGatewaySettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BepaidService implements PaymentGatewayInterface
{
    /**
     * Инициализировать настройки SDK из config (.env)
     *
     * shop_id и secret_key используются для:
     * - API запросов к bePaid (создание платежей, проверка статуса, возвраты)
     * - Проверки webhook (BepaidWebhookController::validateBasicAuth)
     *
     * bePaid отправляет webhook с заголовком: Authorization: Basic base64(shop_id:secret_key)
     */
    public function initializeSettings(): void
    {
        if (! config('bepaid.enabled')) {
            throw new \RuntimeException('bePaid отключен. Установите BEPAID_ENABLED=true в .env.');
        }

        $shopId = config('bepaid.shop_id');
        $secretKey = config('bepaid.secret_key');

        if (empty($shopId) || empty($secretKey)) {
            throw new \RuntimeException('bePaid настройки не заполнены. Укажите BEPAID_SHOP_ID и BEPAID_SECRET_KEY в .env.');
        }

        $shopId = trim((string) $shopId);
        if (! is_numeric($shopId)) {
            throw new \RuntimeException('BEPAID_SHOP_ID должен быть числом. Текущее значение: '.$shopId);
        }

        BeGatewaySettings::$shopId = $shopId;
        BeGatewaySettings::$shopKey = (string) trim($secretKey);
        BeGatewaySettings::$gatewayBase = config('bepaid.gateway_base');
        BeGatewaySettings::$checkoutBase = config('bepaid.checkout_base');

        if (config('bepaid.logging.enabled')) {
            Log::info('bePaid settings initialized', [
                'test_mode' => config('bepaid.test_mode'),
                'shop_id' => $shopId,
                'has_secret_key' => ! empty($secretKey),
                'gateway_base' => BeGatewaySettings::$gatewayBase,
                'checkout_base' => BeGatewaySettings::$checkoutBase,
            ]);
        }
    }

    /**
     * Создать платежный токен для оплаты (редирект или виджет)
     */
    public function createPaymentToken(Invoice $invoice, string $method = 'redirect'): array
    {
        $this->initializeSettings();

        // Проверяем, что настройки действительно установлены
        if (empty(BeGatewaySettings::$shopId) || empty(BeGatewaySettings::$shopKey)) {
            Log::error('bePaid settings not properly initialized', [
                'shop_id_set' => ! empty(BeGatewaySettings::$shopId),
                'shop_key_set' => ! empty(BeGatewaySettings::$shopKey),
            ]);
            throw new \RuntimeException('Настройки bePaid не инициализированы. Проверьте BEPAID_SHOP_ID и BEPAID_SECRET_KEY в .env.');
        }

        $transaction = new GetPaymentToken;

        // Устанавливаем сумму и валюту
        // bePaid требует сумму в минимальных единицах валюты (копейки для BYN, центы для USD)
        $amountInCents = (int) round($invoice->amount * 100);
        $transaction->money->setAmount($amountInCents);
        $transaction->money->setCurrency($invoice->currency);

        // Описание платежа
        $description = "Оплата подписки: {$invoice->plan->name}";
        $transaction->setDescription($description);

        // Уникальный ID транзакции
        $transaction->setTrackingId("invoice_{$invoice->id}");

        // Язык интерфейса чекаута
        $transaction->setLanguage(config('bepaid.checkout_language', 'ru'));

        // URL для уведомлений (webhook)
        //
        // ВАЖНО: Как работает webhook URL и HTTP Basic Auth
        //
        // 1. Мы указываем webhook URL через setNotificationUrl()
        //    - URL берется из настроек БД (webhook_url) или из конфига
        //    - bePaid сохраняет этот URL и будет отправлять на него уведомления
        //
        // 2. Когда bePaid отправляет webhook на этот URL:
        //    - bePaid АВТОМАТИЧЕСКИ добавляет заголовок Authorization с Basic Auth
        //    - Формат: Authorization: Basic base64(shop_id:secret_key)
        //    - shop_id и secret_key берутся из настроек магазина в системе bePaid
        //    - Это те же credentials, которые мы указываем в админ-панели
        //
        // 3. На нашей стороне (BepaidWebhookController::validateBasicAuth):
        //    - Мы извлекаем shop_id и secret_key из заголовка Authorization
        //    - Сравниваем с config (BEPAID_* из .env)
        //    - Если совпадают - обрабатываем webhook, если нет - возвращаем 401
        //
        // ВАЖНО: Мы НЕ задаем Basic Auth здесь!
        // bePaid сам использует shop_id и secret_key из настроек магазина.
        // Мы только указываем URL, куда отправлять webhook.
        $webhookUrl = config('bepaid.webhook.url');
        if ($webhookUrl) {
            // url() - формирует полный URL (например: https://example.com/webhooks/bepaid)
            $transaction->setNotificationUrl(url($webhookUrl));
        }

        // URL для возврата после оплаты
        $callbackUrls = config('bepaid.callback_urls');
        $transaction->setSuccessUrl(url($callbackUrls['success'])."?invoice={$invoice->id}");
        $transaction->setDeclineUrl(url($callbackUrls['decline'])."?invoice={$invoice->id}");
        $transaction->setFailUrl(url($callbackUrls['fail'])."?invoice={$invoice->id}");
        $transaction->setCancelUrl(url($callbackUrls['cancel'])."?invoice={$invoice->id}");

        // Информация о клиенте
        $user = $invoice->user;
        $transaction->customer->setFirstName($user->name ?? 'Пользователь');
        $transaction->customer->setEmail($user->email);
        $transaction->customer->setIp(request()->ip() ?? '127.0.0.1');

        // Отправляем запрос
        $response = $transaction->submit();

        if ($response->isSuccess()) {
            // Сохраняем токен в инвойс
            $invoice->update([
                'bepaid_payment_token' => $response->getToken(),
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'bepaid_response' => [
                        'token' => $response->getToken(),
                        'redirect_url' => $response->getRedirectUrl(),
                    ],
                ]),
            ]);

            if (config('bepaid.logging.enabled')) {
                Log::info('bePaid payment token created', [
                    'invoice_id' => $invoice->id,
                    'token' => $response->getToken(),
                    'amount' => $invoice->amount,
                    'amount_in_cents' => $amountInCents,
                    'currency' => $invoice->currency,
                ]);
            }

            return [
                'success' => true,
                'token' => $response->getToken(),
                'redirect_url' => $response->getRedirectUrl(),
            ];
        }

        $errorMessage = $response->getMessage() ?? 'Неизвестная ошибка при создании платежа';
        $errorCode = method_exists($response, 'getResponseCode') ? $response->getResponseCode() : null;

        // Логируем детальную информацию об ошибке
        Log::error('bePaid payment token creation failed', [
            'invoice_id' => $invoice->id,
            'error' => $errorMessage,
            'error_code' => $errorCode,
            'response' => method_exists($response, 'getResponse') ? $response->getResponse() : null,
            'shop_id' => BeGatewaySettings::$shopId ?? null,
            'test_mode' => config('bepaid.test_mode'),
        ]);

        throw new \RuntimeException("Ошибка создания платежа: {$errorMessage}");
    }

    /**
     * Проверить статус платежа по UID транзакции
     */
    public function checkPaymentStatus(string $transactionUid): array
    {
        $this->initializeSettings();

        $query = new QueryByUid;
        $query->setUid($transactionUid);

        $response = $query->submit();

        if (! $response->isSuccess()) {
            throw new \RuntimeException('Не удалось проверить статус платежа: '.$response->getMessage());
        }

        return [
            'uid' => $response->getUid(),
            'status' => $response->getStatus(),
            'message' => $response->getMessage(),
            'amount' => $response->getAmount(),
            'currency' => $response->getCurrency(),
            'paid' => $response->isSuccess() && $response->getStatus() === 'successful',
            'failed' => $response->isFailed(),
        ];
    }

    /**
     * Возврат средств
     */
    public function refund(Invoice $invoice, ?float $amount = null): array
    {
        if (! $invoice->isPaid()) {
            throw new \RuntimeException('Можно вернуть только оплаченный инвойс');
        }

        if (! $invoice->bepaid_transaction_id) {
            throw new \RuntimeException('У инвойса нет ID транзакции bePaid');
        }

        $this->initializeSettings();

        $refund = new RefundOperation;
        $refund->setParentUid($invoice->bepaid_transaction_id);

        // bePaid требует сумму в минимальных единицах валюты (копейки для BYN, центы для USD)
        $refundAmount = $amount ?? $invoice->amount;
        $refundAmountInCents = (int) round($refundAmount * 100);
        $refund->money->setAmount($refundAmountInCents);
        $refund->money->setCurrency($invoice->currency);

        $refund->setReason('Возврат средств по запросу администратора');

        $response = $refund->submit();

        if ($response->isSuccess()) {
            $invoice->update([
                'status' => 'refunded',
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'refund' => [
                        'uid' => $response->getUid(),
                        'amount' => $refundAmount,
                        'amount_in_cents' => $refundAmountInCents,
                        'refunded_at' => now()->toIso8601String(),
                    ],
                ]),
            ]);

            if (config('bepaid.logging.enabled')) {
                Log::info('bePaid refund successful', [
                    'invoice_id' => $invoice->id,
                    'refund_uid' => $response->getUid(),
                    'amount' => $refundAmount,
                ]);
            }

            return [
                'success' => true,
                'uid' => $response->getUid(),
                'amount' => $refundAmount,
                'message' => 'Возврат успешно выполнен',
            ];
        }

        $errorMessage = $response->getMessage() ?? 'Неизвестная ошибка при возврате';

        if (config('bepaid.logging.enabled')) {
            Log::error('bePaid refund failed', [
                'invoice_id' => $invoice->id,
                'error' => $errorMessage,
            ]);
        }

        throw new \RuntimeException("Ошибка возврата средств: {$errorMessage}");
    }

    /**
     * Валидация webhook подписи (если требуется)
     * bePaid отправляет webhook с базовой HTTP аутентификацией
     */
    public function validateWebhookRequest(array $data): bool
    {
        // bePaid использует HTTP Basic Auth для webhook
        // Проверка происходит на уровне middleware или контроллера
        // Здесь можно добавить дополнительную валидацию данных

        if (empty($data['transaction'])) {
            return false;
        }

        $transaction = $data['transaction'];

        // Проверка обязательных полей
        if (empty($transaction['uid'])) {
            return false;
        }

        if (empty($transaction['tracking_id'])) {
            return false;
        }

        if (empty($transaction['status'])) {
            return false;
        }

        // Проверка наличия суммы для валидации
        if (! isset($transaction['amount'])) {
            return false;
        }

        return true;
    }

    /**
     * Обработать данные webhook от bePaid
     */
    public function processWebhook(array $data): ?Invoice
    {
        if (! $this->validateWebhookRequest($data)) {
            throw new \RuntimeException('Невалидные данные webhook');
        }

        $transaction = $data['transaction'] ?? null;

        if (! $transaction) {
            throw new \RuntimeException('Транзакция не найдена в данных webhook');
        }

        $transactionUid = $transaction['uid'] ?? null;
        $trackingId = $transaction['tracking_id'] ?? null;

        if (! $transactionUid) {
            throw new \RuntimeException('UID транзакции не найден');
        }

        // Извлекаем ID инвойса из tracking_id (формат: invoice_123)
        if (! $trackingId || ! str_starts_with($trackingId, 'invoice_')) {
            throw new \RuntimeException('Неверный формат tracking_id');
        }

        $invoiceId = (int) str_replace('invoice_', '', $trackingId);

        // Используем транзакцию БД и блокировку для защиты от race conditions
        return DB::transaction(function () use ($invoiceId, $transactionUid, $transaction, $data) {
            // Блокируем строку для обновления
            $invoice = Invoice::where('id', $invoiceId)->lockForUpdate()->first();

            if (! $invoice) {
                throw new \RuntimeException("Инвойс #{$invoiceId} не найден");
            }

            // Идемпотентность: если инвойс уже обработан с тем же transaction_id
            if ($invoice->bepaid_transaction_id === $transactionUid) {
                if (config('bepaid.logging.enabled')) {
                    Log::info('bePaid webhook: duplicate transaction, already processed', array_merge($this->webhookLogContext($invoice->id, $transactionUid), [
                        'current_status' => $invoice->status,
                    ]));
                }

                return $invoice;
            }

            // Если инвойс уже оплачен, но приходит другой transaction_id - логируем
            if ($invoice->isPaid() && $invoice->bepaid_transaction_id !== $transactionUid) {
                if (config('bepaid.logging.enabled')) {
                    Log::warning('bePaid webhook: invoice already paid with different transaction', array_merge($this->webhookLogContext($invoice->id, $transactionUid), [
                        'existing_transaction_id' => $invoice->bepaid_transaction_id,
                    ]));
                }

                return $invoice;
            }

            // Проверка суммы платежа
            $webhookAmount = $transaction['amount'] ?? null;
            if ($webhookAmount !== null) {
                // bePaid отправляет сумму в минимальных единицах (копейки/центы)
                $invoiceAmountInCents = (int) round($invoice->amount * 100);
                $webhookAmountInt = (int) $webhookAmount;

                if ($webhookAmountInt !== $invoiceAmountInCents) {
                    Log::error('bePaid webhook: amount mismatch', array_merge($this->webhookLogContext($invoice->id, $transactionUid), [
                        'invoice_amount_cents' => $invoiceAmountInCents,
                        'webhook_amount_cents' => $webhookAmountInt,
                    ]));
                    throw new \RuntimeException("Сумма платежа не совпадает. Ожидалось: {$invoiceAmountInCents}, получено: {$webhookAmountInt}");
                }
            }

            // Проверка валюты (дополнительная защита)
            $webhookCurrency = $transaction['currency'] ?? null;
            if ($webhookCurrency !== null && $webhookCurrency !== $invoice->currency) {
                Log::error('bePaid webhook: currency mismatch', array_merge($this->webhookLogContext($invoice->id, $transactionUid), [
                    'invoice_currency' => $invoice->currency,
                    'webhook_currency' => $webhookCurrency,
                ]));
                throw new \RuntimeException("Валюта платежа не совпадает. Ожидалось: {$invoice->currency}, получено: {$webhookCurrency}");
            }

            // Обновляем статус инвойса
            $status = $transaction['status'] ?? null;
            $newStatus = match ($status) {
                'successful' => 'paid',
                'failed' => 'failed',
                'canceled', 'cancelled' => 'cancelled',
                'expired' => 'failed', // expired обрабатываем как failed
                'pending', 'processing' => 'pending',
                default => 'pending',
            };

            // Логируем неизвестные статусы
            if (! in_array($status, ['successful', 'failed', 'canceled', 'cancelled', 'expired', 'pending', 'processing'])) {
                if (config('bepaid.logging.enabled')) {
                    Log::warning('bePaid webhook: unknown transaction status', array_merge($this->webhookLogContext($invoice->id, $transactionUid), [
                        'status' => $status,
                    ]));
                }
            }

            $updateData = [
                'bepaid_transaction_id' => $transactionUid,
                'status' => $newStatus,
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'webhook_data' => $data,
                    'processed_at' => now()->toIso8601String(),
                ]),
            ];

            if ($newStatus === 'paid') {
                $updateData['paid_at'] = now();
            }

            $invoice->update($updateData);

            if (config('bepaid.logging.enabled')) {
                Log::info('bePaid webhook processed', array_merge($this->webhookLogContext($invoice->id, $transactionUid), [
                    'status' => $newStatus,
                    'original_status' => $status,
                ]));
            }

            return $invoice;
        });
    }

    /**
     * Контекст для структурированного логирования webhook (фильтрация и алерты).
     */
    private function webhookLogContext(int|string $invoiceId, string $transactionUid): array
    {
        return [
            'channel' => 'payment',
            'event' => 'bepaid_webhook',
            'invoice_id' => (int) $invoiceId,
            'transaction_uid' => $transactionUid,
        ];
    }
}
