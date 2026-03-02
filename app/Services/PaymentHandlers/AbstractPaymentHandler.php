<?php

declare(strict_types=1);

namespace App\Services\PaymentHandlers;

use App\Contracts\PaymentHandlerInterface;
use App\Models\Invoice;
use App\Models\PaymentTypeSetting;
use Illuminate\Support\Facades\Log;

/**
 * Базовый класс для обработчиков типов оплат
 */
abstract class AbstractPaymentHandler implements PaymentHandlerInterface
{
    protected ?PaymentTypeSetting $settings = null;

    /**
     * Получить конфигурацию типа оплаты
     */
    protected function getConfig(string $key, mixed $default = null): mixed
    {
        return config("payments.types.{$this->getType()}.{$key}", $default);
    }

    /**
     * Получить настройки типа из БД
     */
    protected function getSettings(): ?PaymentTypeSetting
    {
        if ($this->settings === null) {
            $this->settings = PaymentTypeSetting::findByType($this->getType());
        }

        return $this->settings;
    }

    /**
     * Получить значение из настроек БД или конфига
     */
    protected function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->getSettings();

        if ($settings) {
            $value = $settings->getConfigValue($key);
            if ($value !== null) {
                return $value;
            }
        }

        return $this->getConfig($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName(): string
    {
        return $this->getConfig('name', $this->getType());
    }

    /**
     * {@inheritdoc}
     */
    public function onPaymentCancelled(Invoice $invoice): void
    {
        $invoice->update(['status' => 'cancelled']);

        $this->log('Payment cancelled', ['invoice_id' => $invoice->id]);
    }

    /**
     * {@inheritdoc}
     */
    public function onPaymentRefunded(Invoice $invoice): void
    {
        $invoice->update(['status' => 'refunded']);

        $this->log('Payment refunded', ['invoice_id' => $invoice->id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessRedirectUrl(Invoice $invoice): string
    {
        return route('subscription.index', ['payment' => 'success']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFailRedirectUrl(Invoice $invoice): string
    {
        return route('subscription.index', ['payment' => 'failed']);
    }

    /**
     * {@inheritdoc}
     */
    public function validateData(array $data): array
    {
        return $data;
    }

    /**
     * Логировать сообщение
     */
    protected function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (! config('payments.logging', false)) {
            return;
        }

        $context['payment_type'] = $this->getType();

        Log::{$level}("[PaymentHandler:{$this->getType()}] {$message}", $context);
    }

    /**
     * Получить минимальную сумму
     */
    public function getMinAmount(): ?float
    {
        $settings = $this->getSettings();

        return $settings?->min_amount;
    }

    /**
     * Получить максимальную сумму
     */
    public function getMaxAmount(): ?float
    {
        $settings = $this->getSettings();

        return $settings?->max_amount;
    }

    /**
     * Проверить, допустима ли сумма
     */
    public function isAmountValid(float $amount): bool
    {
        $min = $this->getMinAmount();
        $max = $this->getMaxAmount();

        if ($min !== null && $amount < $min) {
            return false;
        }

        if ($max !== null && $amount > $max) {
            return false;
        }

        return true;
    }

    /**
     * Получить разрешённые шлюзы
     */
    public function getAllowedGateways(): array
    {
        $settings = $this->getSettings();

        if ($settings && ! empty($settings->allowed_gateways)) {
            return $settings->allowed_gateways;
        }

        return $this->getConfig('allowed_gateways', []);
    }

    /**
     * Получить шлюз по умолчанию
     */
    public function getDefaultGateway(): ?string
    {
        $settings = $this->getSettings();

        if ($settings && $settings->default_gateway) {
            return $settings->default_gateway;
        }

        $allowedGateways = $this->getAllowedGateways();

        return $allowedGateways[0] ?? config('payments.default_gateway');
    }
}
