<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use App\DTO\Payment\PaymentResult;
use App\DTO\Payment\PaymentStatus;
use App\DTO\Payment\RefundResult;
use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use BeGateway\GetPaymentToken;
use BeGateway\QueryByUid;
use BeGateway\RefundOperation;
use BeGateway\Settings as BeGatewaySettings;
use Illuminate\Http\Request;

/**
 * Платёжный шлюз bePaid
 */
class BepaidGateway extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'bepaid';
    }

    /**
     * Инициализировать настройки SDK
     */
    protected function initializeSettings(): void
    {
        $shopId = $this->getConfig('shop_id');
        $secretKey = $this->getConfig('secret_key');

        if (empty($shopId) || empty($secretKey)) {
            throw new \RuntimeException('bePaid настройки не заполнены. Укажите BEPAID_SHOP_ID и BEPAID_SECRET_KEY в .env.');
        }

        $shopId = trim((string) $shopId);
        if (! is_numeric($shopId)) {
            throw new \RuntimeException('BEPAID_SHOP_ID должен быть числом. Текущее значение: '.$shopId);
        }

        BeGatewaySettings::$shopId = $shopId;
        BeGatewaySettings::$shopKey = trim((string) $secretKey);

        // Определяем URL-ы API в зависимости от режима
        $testMode = $this->isTestMode();

        BeGatewaySettings::$gatewayBase = $this->getConfig('gateway_base')
            ?: ($testMode ? 'https://gateway.sandbox.bepaid.by' : 'https://gateway.bepaid.by');

        BeGatewaySettings::$checkoutBase = $this->getConfig('checkout_base')
            ?: ($testMode ? 'https://checkout.sandbox.bepaid.by' : 'https://checkout.bepaid.by');

        $this->log('Settings initialized', [
            'test_mode' => $testMode,
            'shop_id' => $shopId,
            'gateway_base' => BeGatewaySettings::$gatewayBase,
            'checkout_base' => BeGatewaySettings::$checkoutBase,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createPayment(Invoice $invoice, array $options = []): PaymentResult
    {
        $this->initializeSettings();

        $transaction = new GetPaymentToken;

        // Сумма и валюта
        $amountInCents = $this->formatAmount((float) $invoice->amount);
        $transaction->money->setAmount($amountInCents);
        $transaction->money->setCurrency($invoice->currency);

        // Описание платежа
        $description = $options['description'] ?? $this->getPaymentDescription($invoice);
        $transaction->setDescription($description);

        // Уникальный ID транзакции
        $transaction->setTrackingId("invoice_{$invoice->id}");

        // Язык интерфейса
        $transaction->setLanguage($this->getConfig('checkout_language', 'ru'));

        // Webhook URL
        $transaction->setNotificationUrl($this->getWebhookUrl());

        // Callback URL-ы
        $transaction->setSuccessUrl($this->getCallbackUrl('success', $invoice));
        $transaction->setDeclineUrl($this->getCallbackUrl('fail', $invoice));
        $transaction->setFailUrl($this->getCallbackUrl('fail', $invoice));
        $transaction->setCancelUrl($this->getCallbackUrl('cancel', $invoice));

        // Информация о клиенте
        $user = $invoice->user;
        $transaction->customer->setFirstName($user->name ?? 'Пользователь');
        $transaction->customer->setEmail($user->email);
        $transaction->customer->setIp(request()->ip() ?? '127.0.0.1');

        // Отправляем запрос
        $response = $transaction->submit();

        if ($response->isSuccess()) {
            $this->log('Payment token created', [
                'invoice_id' => $invoice->id,
                'token' => $response->getToken(),
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
            ]);

            return PaymentResult::success(
                redirectUrl: $response->getRedirectUrl(),
                transactionId: null, // bePaid не даёт ID до оплаты
                paymentToken: $response->getToken(),
                rawResponse: [
                    'token' => $response->getToken(),
                    'redirect_url' => $response->getRedirectUrl(),
                ],
            );
        }

        $errorMessage = $response->getMessage() ?? 'Неизвестная ошибка при создании платежа';

        $this->log('Payment token creation failed', [
            'invoice_id' => $invoice->id,
            'error' => $errorMessage,
        ], 'error');

        return PaymentResult::failure(
            errorMessage: $errorMessage,
            rawResponse: method_exists($response, 'getResponse') ? (array) $response->getResponse() : [],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function checkPaymentStatus(string $transactionId): PaymentStatus
    {
        $this->initializeSettings();

        $query = new QueryByUid;
        $query->setUid($transactionId);

        $response = $query->submit();

        if (! $response->isSuccess()) {
            return new PaymentStatus(
                status: PaymentStatus::STATUS_PENDING,
                transactionId: $transactionId,
                message: $response->getMessage() ?? 'Не удалось проверить статус',
            );
        }

        $status = match ($response->getStatus()) {
            'successful' => PaymentStatus::STATUS_SUCCESSFUL,
            'failed' => PaymentStatus::STATUS_FAILED,
            'canceled', 'cancelled' => PaymentStatus::STATUS_CANCELLED,
            'expired' => PaymentStatus::STATUS_EXPIRED,
            default => PaymentStatus::STATUS_PENDING,
        };

        return new PaymentStatus(
            status: $status,
            transactionId: $response->getUid(),
            amount: $this->parseAmount((int) $response->getAmount()),
            currency: $response->getCurrency(),
            message: $response->getMessage(),
            rawResponse: method_exists($response, 'getResponse') ? (array) $response->getResponse() : [],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function refund(Invoice $invoice, ?float $amount = null): RefundResult
    {
        if (! $this->supportsRefund()) {
            return RefundResult::notSupported();
        }

        if (! $invoice->isPaid()) {
            return RefundResult::failure('Можно вернуть только оплаченный инвойс');
        }

        $transactionId = $invoice->gateway_transaction_id ?? $invoice->bepaid_transaction_id;

        if (! $transactionId) {
            return RefundResult::failure('У инвойса нет ID транзакции');
        }

        $this->initializeSettings();

        $refund = new RefundOperation;
        $refund->setParentUid($transactionId);

        $refundAmount = $amount ?? (float) $invoice->amount;
        $refundAmountInCents = $this->formatAmount($refundAmount);
        $refund->money->setAmount($refundAmountInCents);
        $refund->money->setCurrency($invoice->currency);
        $refund->setReason('Возврат средств по запросу');

        $response = $refund->submit();

        if ($response->isSuccess()) {
            $this->log('Refund successful', [
                'invoice_id' => $invoice->id,
                'refund_uid' => $response->getUid(),
                'amount' => $refundAmount,
            ]);

            return RefundResult::success(
                refundId: $response->getUid(),
                amount: $refundAmount,
                currency: $invoice->currency,
                rawResponse: method_exists($response, 'getResponse') ? (array) $response->getResponse() : [],
            );
        }

        $errorMessage = $response->getMessage() ?? 'Неизвестная ошибка при возврате';

        $this->log('Refund failed', [
            'invoice_id' => $invoice->id,
            'error' => $errorMessage,
        ], 'error');

        return RefundResult::failure($errorMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function validateWebhook(Request $request): bool
    {
        // bePaid использует HTTP Basic Auth
        $authHeader = $request->header('Authorization');

        if (! $authHeader || ! str_starts_with($authHeader, 'Basic ')) {
            return false;
        }

        $credentials = base64_decode(substr($authHeader, 6));
        if (! $credentials || ! str_contains($credentials, ':')) {
            return false;
        }

        [$shopId, $secretKey] = explode(':', $credentials, 2);

        $expectedShopId = $this->getConfig('shop_id');
        $expectedSecretKey = $this->getConfig('secret_key');

        return $shopId === (string) $expectedShopId && $secretKey === $expectedSecretKey;
    }

    /**
     * {@inheritdoc}
     */
    public function parseWebhook(Request $request): WebhookData
    {
        $data = $request->all();
        $transaction = $data['transaction'] ?? [];

        $status = match ($transaction['status'] ?? null) {
            'successful' => 'successful',
            'failed' => 'failed',
            'canceled', 'cancelled' => 'cancelled',
            'expired' => 'expired',
            default => 'pending',
        };

        // Извлекаем ID инвойса из tracking_id (формат: invoice_123)
        $trackingId = $transaction['tracking_id'] ?? '';
        $orderId = str_starts_with($trackingId, 'invoice_')
            ? str_replace('invoice_', '', $trackingId)
            : $trackingId;

        // Информация о карте
        $card = $transaction['card'] ?? [];

        return new WebhookData(
            status: $status,
            transactionId: $transaction['uid'] ?? null,
            orderId: $orderId,
            amount: isset($transaction['amount']) ? $this->parseAmount((int) $transaction['amount']) : null,
            currency: $transaction['currency'] ?? null,
            message: $transaction['message'] ?? null,
            paymentMethod: $transaction['payment_method_type'] ?? null,
            cardLastFour: $card['last_4'] ?? null,
            cardBrand: $card['brand'] ?? null,
            rawData: $data,
        );
    }

    /**
     * Получить описание платежа
     */
    protected function getPaymentDescription(Invoice $invoice): string
    {
        if ($invoice->plan) {
            return "Оплата подписки: {$invoice->plan->name}";
        }

        return "Оплата #{$invoice->id}";
    }
}
