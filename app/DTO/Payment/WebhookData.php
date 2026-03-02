<?php

namespace App\DTO\Payment;

/**
 * Данные из webhook платёжного шлюза
 */
readonly class WebhookData
{
    public function __construct(
        public string $status,
        public ?string $transactionId = null,
        public ?string $orderId = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $message = null,
        public ?string $paymentMethod = null,
        public ?string $cardLastFour = null,
        public ?string $cardBrand = null,
        public array $rawData = [],
    ) {}

    /**
     * Проверить, успешен ли платёж
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    /**
     * Проверить, провален ли платёж
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'error', 'declined']);
    }

    /**
     * Получить статус для модели Invoice
     */
    public function getInvoiceStatus(): string
    {
        return match ($this->status) {
            'successful' => 'paid',
            'failed', 'error', 'declined' => 'failed',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'transaction_id' => $this->transactionId,
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'message' => $this->message,
            'payment_method' => $this->paymentMethod,
            'card_last_four' => $this->cardLastFour,
            'card_brand' => $this->cardBrand,
        ];
    }
}
