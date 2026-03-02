<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'gateway',
        'enabled',
        'test_mode',
        'priority',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'test_mode' => 'boolean',
        'priority' => 'integer',
        'config' => 'array',
    ];

    /**
     * Получить настройку шлюза по ключу
     */
    public static function findByGateway(string $gateway): ?self
    {
        return static::where('gateway', $gateway)->first();
    }

    /**
     * Получить все включённые шлюзы
     */
    public static function getEnabled()
    {
        return static::where('enabled', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * Получить значение из config по ключу
     */
    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Установить значение в config по ключу
     */
    public function setConfigValue(string $key, mixed $value): self
    {
        $config = $this->config ?? [];
        data_set($config, $key, $value);
        $this->config = $config;

        return $this;
    }

    /**
     * Получить display name из конфига приложения
     */
    public function getDisplayNameAttribute(): string
    {
        return config("payments.gateways.{$this->gateway}.display_name", $this->gateway);
    }

    /**
     * Проверить, настроен ли шлюз (есть ли креды)
     */
    public function isConfigured(): bool
    {
        return match ($this->gateway) {
            'bepaid' => ! empty(config('payments.gateways.bepaid.shop_id'))
                && ! empty(config('payments.gateways.bepaid.secret_key')),
            'freekassa' => ! empty(config('payments.gateways.freekassa.merchant_id'))
                && ! empty(config('payments.gateways.freekassa.secret_word_1'))
                && ! empty(config('payments.gateways.freekassa.secret_word_2')),
            default => false,
        };
    }

    /**
     * Получить эффективный test_mode (с учётом fallback на конфиг)
     */
    public function getEffectiveTestMode(): bool
    {
        if ($this->test_mode !== null) {
            return $this->test_mode;
        }

        return config("payments.gateways.{$this->gateway}.test_mode", true);
    }
}
