<?php

namespace App\DTO\Payment;

/**
 * Результат возврата средств
 */
readonly class RefundResult
{
    public function __construct(
        public bool $success,
        public ?string $refundId = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $errorMessage = null,
        public ?string $errorCode = null,
        public array $rawResponse = [],
    ) {}

    /**
     * Создать успешный результат
     */
    public static function success(
        string $refundId,
        float $amount,
        string $currency,
        array $rawResponse = [],
    ): self {
        return new self(
            success: true,
            refundId: $refundId,
            amount: $amount,
            currency: $currency,
            rawResponse: $rawResponse,
        );
    }

    /**
     * Создать неуспешный результат
     */
    public static function failure(
        string $errorMessage,
        ?string $errorCode = null,
        array $rawResponse = [],
    ): self {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            rawResponse: $rawResponse,
        );
    }

    /**
     * Создать результат "не поддерживается"
     */
    public static function notSupported(): self
    {
        return new self(
            success: false,
            errorMessage: 'Refund is not supported by this payment gateway',
            errorCode: 'not_supported',
        );
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'refund_id' => $this->refundId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
        ];
    }
}
