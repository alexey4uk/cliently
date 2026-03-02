<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTypeSetting extends Model
{
    protected $fillable = [
        'type',
        'enabled',
        'default_gateway',
        'allowed_gateways',
        'min_amount',
        'max_amount',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'allowed_gateways' => 'array',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'config' => 'array',
    ];

    /**
     * Получить настройку типа по ключу
     */
    public static function findByType(string $type): ?self
    {
        return static::where('type', $type)->first();
    }

    /**
     * Получить все включённые типы оплат
     */
    public static function getEnabled()
    {
        return static::where('enabled', true)->get();
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
    public function getNameAttribute(): string
    {
        return config("payments.types.{$this->type}.name", $this->type);
    }

    /**
     * Получить описание из конфига приложения
     */
    public function getDescriptionAttribute(): string
    {
        return config("payments.types.{$this->type}.description", '');
    }

    /**
     * Получить эффективный список разрешённых шлюзов
     * (из БД или fallback на конфиг)
     */
    public function getEffectiveAllowedGateways(): array
    {
        if (! empty($this->allowed_gateways)) {
            return $this->allowed_gateways;
        }

        return config("payments.types.{$this->type}.allowed_gateways", []);
    }

    /**
     * Получить эффективный шлюз по умолчанию
     */
    public function getEffectiveDefaultGateway(): ?string
    {
        if ($this->default_gateway) {
            return $this->default_gateway;
        }

        $allowedGateways = $this->getEffectiveAllowedGateways();

        return $allowedGateways[0] ?? config('payments.default_gateway');
    }

    /**
     * Проверить, допустима ли сумма для этого типа оплаты
     */
    public function isAmountValid(float $amount): bool
    {
        if ($this->min_amount !== null && $amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount !== null && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    /**
     * Проверить, разрешён ли шлюз для этого типа оплаты
     */
    public function isGatewayAllowed(string $gateway): bool
    {
        return in_array($gateway, $this->getEffectiveAllowedGateways());
    }
}
