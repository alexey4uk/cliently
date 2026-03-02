<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentHandlerInterface;
use App\DTO\Payment\PaymentResult;
use App\DTO\Payment\RefundResult;
use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Главный сервис платежей (оркестратор)
 *
 * Координирует работу шлюзов и обработчиков типов оплат
 */
class PaymentService
{
    protected GatewayManager $gatewayManager;

    protected PaymentSettingsService $settingsService;

    /**
     * Зарегистрированные обработчики типов оплат
     */
    protected array $handlers = [];

    public function __construct(
        GatewayManager $gatewayManager,
        PaymentSettingsService $settingsService
    ) {
        $this->gatewayManager = $gatewayManager;
        $this->settingsService = $settingsService;
    }

    /**
     * Зарегистрировать обработчик типа оплаты
     */
    public function registerHandler(PaymentHandlerInterface $handler): void
    {
        $this->handlers[$handler->getType()] = $handler;
    }

    /**
     * Получить обработчик типа оплаты
     */
    public function getHandler(string $type): PaymentHandlerInterface
    {
        if (! isset($this->handlers[$type])) {
            // Пробуем создать из конфига
            $handlerClass = config("payments.types.{$type}.handler");

            if (! $handlerClass || ! class_exists($handlerClass)) {
                throw new \InvalidArgumentException("Обработчик для типа оплаты '{$type}' не найден.");
            }

            $this->handlers[$type] = app($handlerClass);
        }

        return $this->handlers[$type];
    }

    /**
     * Создать платёж для существующего инвойса
     *
     * @param  Invoice  $invoice  Инвойс
     * @param  string|null  $gateway  Название шлюза (опционально)
     * @param  array  $options  Дополнительные опции (payment_system_id и др.)
     */
    public function createPaymentForInvoice(Invoice $invoice, ?string $gateway = null, array $options = []): PaymentResult
    {
        $gatewayName = $gateway ?? $invoice->gateway ?? $this->settingsService->getDefaultGatewayForType($invoice->payment_type ?? 'subscription');

        if (! $gatewayName) {
            return PaymentResult::failure('Нет доступных шлюзов.');
        }

        // Проверяем, включён ли шлюз
        if (! $this->settingsService->isGatewayEnabled($gatewayName)) {
            return PaymentResult::failure("Шлюз '{$gatewayName}' отключён.");
        }

        // Получаем шлюз и создаём платёж
        $gatewayInstance = $this->gatewayManager->get($gatewayName);

        // Получаем описание через обработчик, если есть
        $description = "Оплата #{$invoice->id}";
        $type = $invoice->payment_type ?? 'subscription';

        try {
            $handler = $this->getHandler($type);
            $description = $handler->getPaymentDescription($invoice);
        } catch (\Exception $e) {
            // Используем дефолтное описание
        }

        // Объединяем опции с описанием
        // Для Express Pay: payment_system_id = 'erip' или 'card' -> payment_method
        $paymentMethod = $options['payment_system_id'] ?? null;
        if ($gatewayName === 'expresspay' && in_array($paymentMethod, ['erip', 'card'])) {
            $options['payment_method'] = $paymentMethod;
        }

        $paymentOptions = array_merge($options, [
            'description' => $description,
        ]);

        $result = $gatewayInstance->createPayment($invoice, $paymentOptions);

        // Сохраняем данные платежа в инвойс
        // Не перезаписываем gateway_payment_url если шлюз уже сохранил его (например, ERIP)
        if ($result->success) {
            $updateData = [
                'gateway' => $gatewayName,
                'gateway_transaction_id' => $result->transactionId,
                'gateway_response' => $result->rawResponse,
                'bepaid_payment_token' => $result->paymentToken,
            ];

            // Обновляем URL только если он ещё не установлен шлюзом
            if (empty($invoice->gateway_payment_url) && $result->redirectUrl) {
                $updateData['gateway_payment_url'] = $result->redirectUrl;
            }

            $invoice->update($updateData);
        }

        $this->log('Payment created for invoice', [
            'invoice_id' => $invoice->id,
            'type' => $type,
            'gateway' => $gatewayName,
            'payment_system_id' => $options['payment_system_id'] ?? null,
            'success' => $result->success,
            'error' => $result->errorMessage,
        ]);

        return $result;
    }

    /**
     * Создать платёж (полный цикл: инвойс + платёж)
     */
    public function createPayment(
        string $type,
        User $user,
        array $data,
        ?string $gateway = null
    ): PaymentResult {
        // Проверяем, включён ли тип оплаты
        if (! $this->settingsService->isTypeEnabled($type)) {
            return PaymentResult::failure("Тип оплаты '{$type}' отключён.");
        }

        // Получаем обработчик
        $handler = $this->getHandler($type);

        // Валидируем данные
        $validatedData = $handler->validateData($data);

        // Определяем шлюз
        $gatewayName = $gateway ?? $this->settingsService->getDefaultGatewayForType($type);

        if (! $gatewayName) {
            return PaymentResult::failure("Нет доступных шлюзов для типа оплаты '{$type}'.");
        }

        // Проверяем, разрешён ли шлюз для этого типа
        $allowedGateways = $this->settingsService->getAllowedGatewaysForType($type);
        if (! in_array($gatewayName, $allowedGateways)) {
            return PaymentResult::failure("Шлюз '{$gatewayName}' не разрешён для типа оплаты '{$type}'.");
        }

        // Создаём инвойс через обработчик
        $invoice = $handler->createInvoice($user, $validatedData);

        // Устанавливаем тип оплаты и шлюз
        $invoice->update([
            'payment_type' => $type,
            'gateway' => $gatewayName,
        ]);

        // Получаем шлюз и создаём платёж
        $gatewayInstance = $this->gatewayManager->get($gatewayName);

        $result = $gatewayInstance->createPayment($invoice, [
            'description' => $handler->getPaymentDescription($invoice),
        ]);

        // Сохраняем данные платежа в инвойс
        if ($result->success) {
            $invoice->update([
                'gateway_transaction_id' => $result->transactionId,
                'gateway_payment_url' => $result->redirectUrl,
                'gateway_response' => $result->rawResponse,
                // Для обратной совместимости с bePaid
                'bepaid_payment_token' => $result->paymentToken,
            ]);
        }

        $this->log('Payment created', [
            'invoice_id' => $invoice->id,
            'type' => $type,
            'gateway' => $gatewayName,
            'success' => $result->success,
        ]);

        return $result;
    }

    /**
     * Обработать webhook
     */
    public function processWebhook(string $gateway, WebhookData $webhookData): ?Invoice
    {
        $orderId = $webhookData->orderId;

        if (! $orderId) {
            throw new \RuntimeException('ID заказа не найден в данных webhook');
        }

        return DB::transaction(function () use ($gateway, $webhookData, $orderId) {
            // Находим инвойс
            $invoice = Invoice::where('id', $orderId)->lockForUpdate()->first();

            if (! $invoice) {
                throw new \RuntimeException("Инвойс #{$orderId} не найден");
            }

            // Проверяем шлюз
            $invoiceGateway = $invoice->gateway ?? 'bepaid'; // fallback для старых инвойсов
            if ($invoiceGateway !== $gateway) {
                $this->log('Webhook gateway mismatch', [
                    'invoice_id' => $invoice->id,
                    'expected' => $invoiceGateway,
                    'received' => $gateway,
                ], 'warning');
            }

            // Идемпотентность: проверяем, не обработан ли уже
            $existingTransactionId = $invoice->gateway_transaction_id ?? $invoice->bepaid_transaction_id;
            if ($existingTransactionId === $webhookData->transactionId && $invoice->isPaid()) {
                $this->log('Webhook duplicate, already processed', [
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $webhookData->transactionId,
                ]);

                return $invoice;
            }

            // Проверяем сумму
            if ($webhookData->amount !== null) {
                $invoiceAmount = (float) $invoice->amount;
                if (abs($webhookData->amount - $invoiceAmount) > 0.01) {
                    throw new \RuntimeException(
                        "Сумма платежа не совпадает. Ожидалось: {$invoiceAmount}, получено: {$webhookData->amount}"
                    );
                }
            }

            // Проверяем валюту
            if ($webhookData->currency !== null && $webhookData->currency !== $invoice->currency) {
                throw new \RuntimeException(
                    "Валюта платежа не совпадает. Ожидалось: {$invoice->currency}, получено: {$webhookData->currency}"
                );
            }

            // Обновляем инвойс
            $newStatus = $webhookData->getInvoiceStatus();
            $updateData = [
                'status' => $newStatus,
                'gateway_transaction_id' => $webhookData->transactionId,
                'gateway_response' => $webhookData->rawData,
                // Для обратной совместимости
                'bepaid_transaction_id' => $webhookData->transactionId,
            ];

            if ($newStatus === 'paid') {
                $updateData['paid_at'] = now();
            }

            $invoice->update($updateData);

            // Вызываем обработчик типа оплаты
            $type = $invoice->payment_type ?? 'subscription';
            $handler = $this->getHandler($type);

            if ($webhookData->isSuccessful()) {
                $handler->onPaymentSuccess($invoice, $webhookData);
            } else {
                $handler->onPaymentFailed($invoice, $webhookData);
            }

            $this->log('Webhook processed', [
                'invoice_id' => $invoice->id,
                'gateway' => $gateway,
                'status' => $newStatus,
                'transaction_id' => $webhookData->transactionId,
            ]);

            return $invoice;
        });
    }

    /**
     * Выполнить возврат средств
     */
    public function refund(Invoice $invoice, ?float $amount = null): RefundResult
    {
        if (! $invoice->isPaid()) {
            return RefundResult::failure('Можно вернуть только оплаченный инвойс');
        }

        $gatewayName = $invoice->gateway ?? 'bepaid';
        $gateway = $this->gatewayManager->get($gatewayName);

        if (! $gateway->supportsRefund()) {
            return RefundResult::failure("Шлюз '{$gatewayName}' не поддерживает возвраты");
        }

        $result = $gateway->refund($invoice, $amount);

        if ($result->success) {
            $invoice->update([
                'status' => 'refunded',
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'refund' => [
                        'refund_id' => $result->refundId,
                        'amount' => $result->amount,
                        'refunded_at' => now()->toIso8601String(),
                    ],
                ]),
            ]);

            // Уведомляем обработчик
            $type = $invoice->payment_type ?? 'subscription';
            $handler = $this->getHandler($type);
            $handler->onPaymentRefunded($invoice);
        }

        $this->log('Refund processed', [
            'invoice_id' => $invoice->id,
            'gateway' => $gatewayName,
            'success' => $result->success,
            'amount' => $result->amount,
        ]);

        return $result;
    }

    /**
     * Получить доступные шлюзы для типа оплаты
     */
    public function getAvailableGatewaysForType(string $type): array
    {
        $gateways = $this->settingsService->getAllowedGatewaysForType($type);

        return array_map(function ($name) {
            $gateway = $this->gatewayManager->get($name);

            return [
                'name' => $name,
                'display_name' => $gateway->getDisplayName(),
                'currencies' => $gateway->getSupportedCurrencies(),
                'supports_widget' => config("payments.gateways.{$name}.supports_widget", false),
            ];
        }, $gateways);
    }

    /**
     * Логировать сообщение
     */
    protected function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (! config('payments.logging', false)) {
            return;
        }

        $context['service'] = 'PaymentService';

        Log::{$level}("[Payment] {$message}", $context);
    }
}
