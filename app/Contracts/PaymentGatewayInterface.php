<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\Payment\PaymentResult;
use App\DTO\Payment\PaymentStatus;
use App\DTO\Payment\RefundResult;
use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * Контракт платёжного шлюза.
 *
 * Каждый шлюз (bePaid, FreeKassa и т.д.) реализует этот интерфейс.
 */
interface PaymentGatewayInterface
{
    /**
     * Получить идентификатор шлюза (например, 'bepaid', 'freekassa')
     */
    public function getName(): string;

    /**
     * Получить отображаемое название шлюза
     */
    public function getDisplayName(): string;

    /**
     * Проверить, включён ли тестовый режим
     */
    public function isTestMode(): bool;

    /**
     * Получить список поддерживаемых валют
     */
    public function getSupportedCurrencies(): array;

    /**
     * Проверить, поддерживает ли шлюз возвраты
     */
    public function supportsRefund(): bool;

    /**
     * Создать платёж и получить URL для оплаты
     */
    public function createPayment(Invoice $invoice, array $options = []): PaymentResult;

    /**
     * Проверить статус платежа по ID транзакции
     */
    public function checkPaymentStatus(string $transactionId): PaymentStatus;

    /**
     * Выполнить возврат средств
     */
    public function refund(Invoice $invoice, ?float $amount = null): RefundResult;

    /**
     * Валидировать входящий webhook запрос
     */
    public function validateWebhook(Request $request): bool;

    /**
     * Распарсить данные из webhook запроса
     */
    public function parseWebhook(Request $request): WebhookData;

    /**
     * Получить URL для webhook
     */
    public function getWebhookUrl(): string;

    /**
     * Получить URL для callback (success/fail/cancel)
     */
    public function getCallbackUrl(string $type, Invoice $invoice): string;
}
