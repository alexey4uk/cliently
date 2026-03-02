<?php

namespace App\DTO\Payment;

/**
 * Статус платежа
 */
readonly class PaymentStatus
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_SUCCESSFUL = 'successful';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REFUNDED = 'refunded';

    public const STATUS_EXPIRED = 'expired';

    public function __construct(
        public string $status,
        public ?string $transactionId = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $message = null,
        public array $rawResponse = [],
    ) {}

    /**
     * Проверить, успешен ли платёж
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    /**
     * Проверить, оплачен ли платёж (алиас для isSuccessful)
     */
    public function isPaid(): bool
    {
        return $this->isSuccessful();
    }

    /**
     * Проверить, ожидает ли платёж
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Проверить, провален ли платёж
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Проверить, отменён ли платёж
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Проверить, возвращён ли платёж
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Проверить, истёк ли платёж
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Проверить, финальный ли статус (не изменится)
     */
    public function isFinal(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUCCESSFUL,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
            self::STATUS_EXPIRED,
        ]);
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'transaction_id' => $this->transactionId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'message' => $this->message,
        ];
    }
}
