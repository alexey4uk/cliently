<?php

namespace App\DTO\Payment;

/**
 * Результат создания платежа
 */
readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public ?string $redirectUrl = null,
        public ?string $transactionId = null,
        public ?string $paymentToken = null,
        public ?string $errorMessage = null,
        public ?string $errorCode = null,
        public array $rawResponse = [],
    ) {}

    /**
     * Создать успешный результат
     */
    public static function success(
        string $redirectUrl,
        ?string $transactionId = null,
        ?string $paymentToken = null,
        array $rawResponse = [],
    ): self {
        return new self(
            success: true,
            redirectUrl: $redirectUrl,
            transactionId: $transactionId,
            paymentToken: $paymentToken,
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
     * Проверить, успешен ли результат
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'redirect_url' => $this->redirectUrl,
            'transaction_id' => $this->transactionId,
            'payment_token' => $this->paymentToken,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
        ];
    }
}
