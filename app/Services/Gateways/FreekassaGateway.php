<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use App\DTO\Payment\PaymentResult;
use App\DTO\Payment\PaymentStatus;
use App\DTO\Payment\RefundResult;
use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Платёжный шлюз FreeKassa
 *
 * Документация: https://docs.freekassa.net/
 */
class FreekassaGateway extends AbstractGateway
{
    /**
     * URL для SCI (форма оплаты)
     */
    protected const PAYMENT_URL = 'https://pay.fk.money/';

    /**
     * URL для API v1
     */
    protected const API_URL = 'https://api.fk.life/v1/';

    /**
     * ID платёжной системы по умолчанию (4 = VISA RUB)
     */
    protected const DEFAULT_PAYMENT_SYSTEM = 4;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'freekassa';
    }

    /**
     * {@inheritdoc}
     */
    public function createPayment(Invoice $invoice, array $options = []): PaymentResult
    {
        $apiKey = $this->getConfig('api_key');
        $merchantId = $this->getConfig('merchant_id');

        // Если есть API ключ — используем новое API
        if (! empty($apiKey) && ! empty($merchantId)) {
            return $this->createPaymentViaApi($invoice, $options);
        }

        // Иначе используем старый SCI метод
        return $this->createPaymentViaSci($invoice, $options);
    }

    /**
     * Создание платежа через API v1
     */
    /**
     * Валюты, поддерживаемые FreeKassa
     */
    protected const SUPPORTED_CURRENCIES = ['RUB', 'USD', 'EUR', 'UAH', 'KZT'];

    /**
     * Курсы конвертации в USD (обновлять по необходимости)
     */
    protected const EXCHANGE_RATES_TO_USD = [
        'BYN' => 0.31,  // 1 BYN ≈ 0.31 USD (1 USD ≈ 3.2 BYN)
        'RUB' => 0.011, // 1 RUB ≈ 0.011 USD (1 USD ≈ 90 RUB)
    ];

    /**
     * Конвертировать сумму в поддерживаемую валюту
     */
    protected function convertToSupportedCurrency(float $amount, string $currency): array
    {
        // Если валюта уже поддерживается — возвращаем как есть
        if (in_array($currency, self::SUPPORTED_CURRENCIES)) {
            return ['amount' => $amount, 'currency' => $currency];
        }

        // Конвертируем в USD
        $rate = self::EXCHANGE_RATES_TO_USD[$currency] ?? null;

        if ($rate) {
            $convertedAmount = round($amount * $rate, 2);

            return ['amount' => $convertedAmount, 'currency' => 'USD'];
        }

        // Если курс неизвестен — просто меняем валюту на USD (не рекомендуется)
        return ['amount' => $amount, 'currency' => 'USD'];
    }

    protected function createPaymentViaApi(Invoice $invoice, array $options = []): PaymentResult
    {
        $apiKey = $this->getConfig('api_key');
        $merchantId = $this->getConfig('merchant_id');

        // Конвертируем в поддерживаемую валюту
        $converted = $this->convertToSupportedCurrency((float) $invoice->amount, $invoice->currency);
        $amount = $converted['amount'];
        $currency = $converted['currency'];

        $orderId = (string) $invoice->id;
        $email = $invoice->user->email ?? 'customer@example.com';
        $ip = request()->ip() ?? '127.0.0.1';

        // ID платёжной системы (можно передать в options или использовать дефолт)
        $paymentSystemId = $options['payment_system_id'] ?? $this->getConfig('default_payment_system') ?? self::DEFAULT_PAYMENT_SYSTEM;

        $nonce = time();

        // Формируем данные запроса
        $data = [
            'shopId' => (int) $merchantId,
            'nonce' => $nonce,
            'paymentId' => $orderId,
            'i' => (int) $paymentSystemId,
            'email' => $email,
            'ip' => $ip,
            'amount' => $amount,
            'currency' => $currency,
        ];

        // Формируем подпись: сортируем по ключам, объединяем значения через |, хешируем HMAC SHA256
        ksort($data);
        $signString = implode('|', $data);
        $signature = hash_hmac('sha256', $signString, $apiKey);
        $data['signature'] = $signature;

        $this->log('Creating payment via API', [
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'currency' => $currency,
            'payment_system' => $paymentSystemId,
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::API_URL.'orders/create', $data);

            $responseData = $response->json();

            $this->log('API response', [
                'invoice_id' => $invoice->id,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (! $response->successful() || ($responseData['type'] ?? '') !== 'success') {
                $errorMessage = $responseData['message'] ?? $responseData['msg'] ?? 'Неизвестная ошибка API';

                return PaymentResult::failure(
                    errorMessage: "Ошибка FreeKassa API: {$errorMessage}",
                    rawResponse: $responseData,
                );
            }

            // Формируем URL для оплаты
            $orderId = $responseData['orderId'] ?? null;
            $orderHash = $responseData['orderHash'] ?? null;
            $location = $responseData['location'] ?? null;

            if ($location) {
                $redirectUrl = $location;
            } elseif ($orderId && $orderHash) {
                $redirectUrl = "https://pay.freekassa.net/form/{$orderId}/{$orderHash}";
            } else {
                return PaymentResult::failure(
                    errorMessage: 'FreeKassa API не вернул URL для оплаты',
                    rawResponse: $responseData,
                );
            }

            return PaymentResult::success(
                redirectUrl: $redirectUrl,
                transactionId: (string) $orderId,
                rawResponse: $responseData,
            );
        } catch (\Exception $e) {
            $this->log('API request failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ], 'error');

            return PaymentResult::failure(
                errorMessage: 'Ошибка соединения с FreeKassa: '.$e->getMessage(),
            );
        }
    }

    /**
     * Создание платежа через SCI (старый метод)
     */
    protected function createPaymentViaSci(Invoice $invoice, array $options = []): PaymentResult
    {
        $merchantId = $this->getConfig('merchant_id');
        $secretWord1 = $this->getConfig('secret_word_1');

        if (empty($merchantId) || empty($secretWord1)) {
            return PaymentResult::failure(
                'FreeKassa настройки не заполнены. Укажите FREEKASSA_MERCHANT_ID и FREEKASSA_SECRET_1 в .env.'
            );
        }

        // Конвертируем в поддерживаемую валюту
        $converted = $this->convertToSupportedCurrency((float) $invoice->amount, $invoice->currency);
        $amount = $converted['amount'];
        $currency = $converted['currency'];

        $orderId = $invoice->id;

        // Формирование подписи: md5(merchant_id:amount:secret_word_1:currency:order_id)
        $sign = md5(implode(':', [
            $merchantId,
            $amount,
            $secretWord1,
            $currency,
            $orderId,
        ]));

        $params = [
            'm' => $merchantId,
            'oa' => $amount,
            'currency' => $currency,
            'o' => $orderId,
            's' => $sign,
            'em' => $invoice->user->email ?? '',
            'us_invoice_id' => $orderId,
            'us_gateway' => 'freekassa',
        ];

        // Добавляем язык если указан
        $language = $options['language'] ?? 'ru';
        if ($language) {
            $params['lang'] = $language;
        }

        $redirectUrl = self::PAYMENT_URL.'?'.http_build_query($params);

        $this->log('Payment URL created (SCI)', [
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return PaymentResult::success(
            redirectUrl: $redirectUrl,
            transactionId: null, // FreeKassa SCI не даёт ID до оплаты
            rawResponse: ['params' => $params],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function checkPaymentStatus(string $transactionId): PaymentStatus
    {
        $apiKey = $this->getConfig('api_key');
        $merchantId = $this->getConfig('merchant_id');

        if (empty($apiKey) || empty($merchantId)) {
            return new PaymentStatus(
                status: PaymentStatus::STATUS_PENDING,
                transactionId: $transactionId,
                message: 'API ключ не настроен для проверки статуса',
            );
        }

        try {
            $nonce = time();
            $data = [
                'shopId' => (int) $merchantId,
                'nonce' => $nonce,
                'orderId' => (int) $transactionId,
            ];

            ksort($data);
            $signature = hash_hmac('sha256', implode('|', $data), $apiKey);
            $data['signature'] = $signature;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::API_URL.'orders', $data);

            if (! $response->successful()) {
                return new PaymentStatus(
                    status: PaymentStatus::STATUS_PENDING,
                    transactionId: $transactionId,
                    message: 'Ошибка API: '.$response->status(),
                );
            }

            $responseData = $response->json();
            $order = $responseData['orders'][0] ?? null;

            if (! $order) {
                return new PaymentStatus(
                    status: PaymentStatus::STATUS_PENDING,
                    transactionId: $transactionId,
                    message: 'Заказ не найден',
                );
            }

            // Статусы FreeKassa: 0 - новый, 1 - оплачен, 6 - возврат, 8 - ошибка, 9 - отмена
            $status = match ($order['status'] ?? null) {
                1 => PaymentStatus::STATUS_SUCCESSFUL,
                0 => PaymentStatus::STATUS_PENDING,
                6 => PaymentStatus::STATUS_REFUNDED,
                8 => PaymentStatus::STATUS_FAILED,
                9 => PaymentStatus::STATUS_CANCELLED,
                default => PaymentStatus::STATUS_PENDING,
            };

            return new PaymentStatus(
                status: $status,
                transactionId: $transactionId,
                amount: (float) ($order['amount'] ?? 0),
                currency: $order['currency'] ?? null,
                rawResponse: $responseData,
            );
        } catch (\Exception $e) {
            $this->log('Check payment status failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ], 'error');

            return new PaymentStatus(
                status: PaymentStatus::STATUS_PENDING,
                transactionId: $transactionId,
                message: 'Ошибка проверки статуса: '.$e->getMessage(),
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refund(Invoice $invoice, ?float $amount = null): RefundResult
    {
        $apiKey = $this->getConfig('api_key');
        $merchantId = $this->getConfig('merchant_id');

        if (empty($apiKey) || empty($merchantId)) {
            return RefundResult::failure('API ключ не настроен для возвратов');
        }

        $transactionId = $invoice->gateway_transaction_id ?? $invoice->bepaid_transaction_id;

        if (! $transactionId) {
            return RefundResult::failure('ID транзакции не найден');
        }

        try {
            $nonce = time();
            $data = [
                'shopId' => (int) $merchantId,
                'nonce' => $nonce,
                'orderId' => (int) $transactionId,
            ];

            ksort($data);
            $signature = hash_hmac('sha256', implode('|', $data), $apiKey);
            $data['signature'] = $signature;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::API_URL.'orders/refund', $data);

            $responseData = $response->json();

            if (! $response->successful() || ($responseData['type'] ?? '') !== 'success') {
                $errorMessage = $responseData['message'] ?? $responseData['msg'] ?? 'Ошибка возврата';

                return RefundResult::failure($errorMessage);
            }

            return RefundResult::success(
                refundId: (string) ($responseData['id'] ?? $transactionId),
                amount: $amount ?? (float) $invoice->amount,
                currency: $invoice->currency ?? 'BYN',
            );
        } catch (\Exception $e) {
            $this->log('Refund failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ], 'error');

            return RefundResult::failure('Ошибка возврата: '.$e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateWebhook(Request $request): bool
    {
        $secretWord2 = $this->getConfig('secret_word_2');

        if (empty($secretWord2)) {
            $this->log('Webhook validation failed: secret_word_2 not configured', [], 'error');

            return false;
        }

        $merchantId = $request->input('MERCHANT_ID');
        $amount = $request->input('AMOUNT');
        $orderId = $request->input('MERCHANT_ORDER_ID');
        $sign = $request->input('SIGN');

        if (! $merchantId || ! $amount || ! $orderId || ! $sign) {
            $this->log('Webhook validation failed: missing required fields', [
                'has_merchant_id' => ! empty($merchantId),
                'has_amount' => ! empty($amount),
                'has_order_id' => ! empty($orderId),
                'has_sign' => ! empty($sign),
            ], 'warning');

            return false;
        }

        // Проверка подписи: md5(merchant_id:amount:secret_word_2:order_id)
        $expectedSign = md5(implode(':', [
            $merchantId,
            $amount,
            $secretWord2,
            $orderId,
        ]));

        $isValid = hash_equals($expectedSign, $sign);

        if (! $isValid) {
            $this->log('Webhook validation failed: invalid signature', [
                'order_id' => $orderId,
            ], 'warning');
        }

        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function parseWebhook(Request $request): WebhookData
    {
        // FreeKassa отправляет только успешные платежи
        $status = 'successful';

        return new WebhookData(
            status: $status,
            transactionId: $request->input('intid'), // ID транзакции в FreeKassa
            orderId: $request->input('MERCHANT_ORDER_ID'),
            amount: (float) $request->input('AMOUNT'),
            currency: $this->getCurrencyByCode($request->input('CUR_ID')),
            paymentMethod: $request->input('P_PHONE') ? 'phone' : 'card',
            rawData: $request->all(),
        );
    }

    /**
     * Получить код валюты по ID платёжной системы
     */
    protected function getCurrencyByCode(?string $currencyId): ?string
    {
        // Валюты по ID платёжной системы (CUR_ID в webhook)
        // Это ID валюты, а не платёжной системы
        $currencies = [
            '1' => 'RUB', // FK WALLET RUB
            '2' => 'USD', // FK WALLET USD
            '3' => 'EUR', // FK WALLET EUR
            '4' => 'RUB', // VISA RUB
            '6' => 'RUB', // Yoomoney
            '7' => 'UAH', // VISA UAH
            '8' => 'RUB', // MasterCard RUB
            '9' => 'UAH', // MasterCard UAH
            '10' => 'RUB', // Qiwi
            '11' => 'EUR', // VISA EUR
            '12' => 'RUB', // МИР
            '32' => 'USD', // VISA USD
            '41' => 'KZT', // VISA/MC KZT
            '42' => 'RUB', // СБП
        ];

        return $currencies[$currencyId] ?? 'RUB';
    }

    /**
     * Проверить IP адрес FreeKassa
     *
     * FreeKassa отправляет уведомления только с определённых IP
     */
    public function isValidIp(string $ip): bool
    {
        $allowedIps = [
            '168.119.157.136',
            '168.119.60.227',
            '178.154.197.79',
            '51.250.54.238',
        ];

        return in_array($ip, $allowedIps);
    }

    /**
     * Получить список доступных платёжных систем
     */
    public function getAvailableCurrencies(): array
    {
        $apiKey = $this->getConfig('api_key');
        $merchantId = $this->getConfig('merchant_id');

        if (empty($apiKey) || empty($merchantId)) {
            return [];
        }

        try {
            $nonce = time();
            $data = [
                'shopId' => (int) $merchantId,
                'nonce' => $nonce,
            ];

            ksort($data);
            $signature = hash_hmac('sha256', implode('|', $data), $apiKey);
            $data['signature'] = $signature;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::API_URL.'currencies', $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return $responseData['currencies'] ?? [];
            }
        } catch (\Exception $e) {
            $this->log('Get currencies failed', ['error' => $e->getMessage()], 'error');
        }

        return [];
    }
}
