<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Invoice;

/**
 * Контракт платёжного шлюза для подписок.
 * Позволяет подменять реализацию (например bePaid) в тестах или при смене провайдера.
 */
interface PaymentGatewayInterface
{
    /**
     * Создать платёжный токен для оплаты (редирект или виджет).
     *
     * @return array{success: bool, token: string, redirect_url: string}
     */
    public function createPaymentToken(Invoice $invoice, string $method = 'redirect'): array;

    /**
     * Валидация входящих данных webhook.
     */
    public function validateWebhookRequest(array $data): bool;

    /**
     * Обработать данные webhook. Возвращает обновлённый Invoice или null.
     */
    public function processWebhook(array $data): ?Invoice;

    /**
     * Проверить статус платежа по UID транзакции.
     *
     * @return array{uid: string, status: string, message: string, amount: int, currency: string, paid: bool, failed: bool}
     */
    public function checkPaymentStatus(string $transactionUid): array;

    /**
     * Возврат средств по инвойсу.
     *
     * @return array{success: bool, uid: string, amount: float, message: string}
     */
    public function refund(Invoice $invoice, ?float $amount = null): array;
}
