<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Collection;

/**
 * Менеджер платёжных шлюзов
 *
 * Отвечает за создание и получение экземпляров шлюзов
 */
class GatewayManager
{
    /**
     * Кэш созданных экземпляров шлюзов
     */
    protected array $gateways = [];

    /**
     * Получить шлюз по имени
     */
    public function get(string $name): PaymentGatewayInterface
    {
        if (! isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->resolve($name);
        }

        return $this->gateways[$name];
    }

    /**
     * Создать экземпляр шлюза
     */
    protected function resolve(string $name): PaymentGatewayInterface
    {
        $config = config("payments.gateways.{$name}");

        if (! $config) {
            throw new \InvalidArgumentException("Платёжный шлюз '{$name}' не найден в конфигурации.");
        }

        $driver = $config['driver'] ?? null;

        if (! $driver) {
            throw new \InvalidArgumentException("Для шлюза '{$name}' не указан драйвер.");
        }

        if (! class_exists($driver)) {
            throw new \InvalidArgumentException("Класс драйвера '{$driver}' для шлюза '{$name}' не найден.");
        }

        $gateway = app($driver);

        if (! $gateway instanceof PaymentGatewayInterface) {
            throw new \InvalidArgumentException(
                "Драйвер '{$driver}' должен реализовывать ".PaymentGatewayInterface::class
            );
        }

        return $gateway;
    }

    /**
     * Проверить, существует ли шлюз
     */
    public function has(string $name): bool
    {
        return config("payments.gateways.{$name}") !== null;
    }

    /**
     * Получить все доступные шлюзы (из конфига)
     */
    public function all(): Collection
    {
        $gateways = config('payments.gateways', []);

        return collect($gateways)->map(function ($config, $name) {
            return [
                'name' => $name,
                'display_name' => $config['display_name'] ?? $name,
                'available' => $config['available'] ?? true,
                'currencies' => $config['currencies'] ?? [],
                'supports_refund' => $config['supports_refund'] ?? false,
                'supports_widget' => $config['supports_widget'] ?? false,
            ];
        });
    }

    /**
     * Получить все доступные шлюзы (available = true в конфиге)
     */
    public function getAvailable(): Collection
    {
        return $this->all()->filter(fn ($g) => $g['available']);
    }

    /**
     * Получить шлюз по умолчанию
     */
    public function getDefault(): PaymentGatewayInterface
    {
        $defaultGateway = config('payments.default_gateway', 'bepaid');

        return $this->get($defaultGateway);
    }

    /**
     * Получить имя шлюза по умолчанию
     */
    public function getDefaultName(): string
    {
        return config('payments.default_gateway', 'bepaid');
    }

    /**
     * Получить шлюз, поддерживающий валюту
     */
    public function getForCurrency(string $currency): ?PaymentGatewayInterface
    {
        $currency = strtoupper($currency);

        foreach ($this->getAvailable() as $name => $config) {
            if (in_array($currency, $config['currencies'])) {
                return $this->get($name);
            }
        }

        return null;
    }

    /**
     * Получить все шлюзы, поддерживающие валюту
     */
    public function getAllForCurrency(string $currency): Collection
    {
        $currency = strtoupper($currency);

        return $this->getAvailable()
            ->filter(fn ($config) => in_array($currency, $config['currencies']))
            ->keys()
            ->map(fn ($name) => $this->get($name));
    }
}
