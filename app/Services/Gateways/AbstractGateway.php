<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Invoice;
use App\Models\PaymentGatewaySetting;
use Illuminate\Support\Facades\Log;

/**
 * Базовый класс для платёжных шлюзов
 */
abstract class AbstractGateway implements PaymentGatewayInterface
{
    protected ?PaymentGatewaySetting $settings = null;

    /**
     * Получить конфигурацию шлюза
     */
    protected function getConfig(string $key, mixed $default = null): mixed
    {
        return config("payments.gateways.{$this->getName()}.{$key}", $default);
    }

    /**
     * Получить настройки шлюза из БД
     */
    protected function getSettings(): ?PaymentGatewaySetting
    {
        if ($this->settings === null) {
            $this->settings = PaymentGatewaySetting::findByGateway($this->getName());
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
        return $this->getConfig('display_name', $this->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function isTestMode(): bool
    {
        $settings = $this->getSettings();

        if ($settings && $settings->test_mode !== null) {
            return $settings->test_mode;
        }

        return (bool) $this->getConfig('test_mode', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCurrencies(): array
    {
        return $this->getConfig('currencies', []);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRefund(): bool
    {
        return (bool) $this->getConfig('supports_refund', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebhookUrl(): string
    {
        $path = $this->getConfig('webhook_url', "/webhooks/{$this->getName()}");

        return rtrim(config('app.url'), '/').$path;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallbackUrl(string $type, Invoice $invoice): string
    {
        $path = config("payments.callbacks.{$type}", "/payment/{invoice}/{$type}");
        $path = str_replace('{invoice}', (string) $invoice->id, $path);

        return rtrim(config('app.url'), '/').$path;
    }

    /**
     * Проверить, включено ли логирование
     */
    protected function isLoggingEnabled(): bool
    {
        return (bool) $this->getConfig('logging', false) || config('payments.logging', false);
    }

    /**
     * Логировать сообщение
     */
    protected function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (! $this->isLoggingEnabled()) {
            return;
        }

        $context['gateway'] = $this->getName();

        Log::{$level}("[Payment:{$this->getName()}] {$message}", $context);
    }

    /**
     * Проверить, поддерживается ли валюта
     */
    public function isCurrencySupported(string $currency): bool
    {
        return in_array(strtoupper($currency), $this->getSupportedCurrencies());
    }

    /**
     * Получить валюту по умолчанию
     */
    public function getDefaultCurrency(): string
    {
        return $this->getConfig('default_currency', config('payments.default_currency', 'BYN'));
    }

    /**
     * Форматировать сумму для шлюза (в копейках/центах)
     */
    protected function formatAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Парсить сумму из шлюза (из копеек/центов)
     */
    protected function parseAmount(int $amount): float
    {
        return $amount / 100;
    }
}
