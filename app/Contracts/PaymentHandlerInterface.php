<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\Payment\WebhookData;
use App\Models\Invoice;
use App\Models\User;

/**
 * Контракт обработчика типа оплаты.
 *
 * Каждый тип оплаты (подписка, покупка, донат и т.д.) реализует этот интерфейс.
 */
interface PaymentHandlerInterface
{
    /**
     * Получить идентификатор типа оплаты (например, 'subscription', 'purchase')
     */
    public function getType(): string;

    /**
     * Получить отображаемое название типа оплаты
     */
    public function getDisplayName(): string;

    /**
     * Создать инвойс для оплаты
     */
    public function createInvoice(User $user, array $data): Invoice;

    /**
     * Обработать успешную оплату
     */
    public function onPaymentSuccess(Invoice $invoice, WebhookData $webhookData): void;

    /**
     * Обработать неуспешную оплату
     */
    public function onPaymentFailed(Invoice $invoice, WebhookData $webhookData): void;

    /**
     * Обработать отмену оплаты
     */
    public function onPaymentCancelled(Invoice $invoice): void;

    /**
     * Обработать возврат средств
     */
    public function onPaymentRefunded(Invoice $invoice): void;

    /**
     * Получить описание платежа для отображения в шлюзе
     */
    public function getPaymentDescription(Invoice $invoice): string;

    /**
     * Получить URL для редиректа после успешной оплаты
     */
    public function getSuccessRedirectUrl(Invoice $invoice): string;

    /**
     * Получить URL для редиректа после неуспешной оплаты
     */
    public function getFailRedirectUrl(Invoice $invoice): string;

    /**
     * Валидировать данные перед созданием инвойса
     */
    public function validateData(array $data): array;
}
