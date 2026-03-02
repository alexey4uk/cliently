<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentGatewaySetting;
use App\Models\PaymentTypeSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Сервис для работы с настройками платежей
 *
 * Объединяет данные из конфига и БД
 */
class PaymentSettingsService
{
    protected GatewayManager $gatewayManager;

    protected bool $initialized = false;

    public function __construct(GatewayManager $gatewayManager)
    {
        $this->gatewayManager = $gatewayManager;
        $this->ensureInitialized();
    }

    /**
     * Убедиться, что настройки инициализированы в БД
     */
    protected function ensureInitialized(): void
    {
        if ($this->initialized) {
            return;
        }

        // Проверяем, существуют ли таблицы (для миграций)
        if (! Schema::hasTable('payment_gateway_settings') || ! Schema::hasTable('payment_type_settings')) {
            return;
        }

        // Если таблицы пустые — инициализируем
        if (PaymentGatewaySetting::count() === 0) {
            $this->initializeDefaults();
        }

        $this->initialized = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Шлюзы
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить, включён ли шлюз
     */
    public function isGatewayEnabled(string $gateway): bool
    {
        // 1. Проверяем доступность в конфиге (жёсткое ограничение)
        if (! config("payments.gateways.{$gateway}.available", true)) {
            return false;
        }

        // 2. Проверяем, настроен ли шлюз
        if (! $this->isGatewayConfigured($gateway)) {
            return false;
        }

        // 3. Проверяем настройку в БД
        $setting = PaymentGatewaySetting::findByGateway($gateway);

        if ($setting) {
            return $setting->enabled;
        }

        // 4. По умолчанию выключен (нужно явно включить в админке)
        return false;
    }

    /**
     * Проверить, настроен ли шлюз (есть ли креды)
     */
    public function isGatewayConfigured(string $gateway): bool
    {
        return match ($gateway) {
            'bepaid' => ! empty(config('payments.gateways.bepaid.shop_id'))
                     && ! empty(config('payments.gateways.bepaid.secret_key')),
            'freekassa' => ! empty(config('payments.gateways.freekassa.merchant_id'))
                        && ! empty(config('payments.gateways.freekassa.secret_word_1'))
                        && ! empty(config('payments.gateways.freekassa.secret_word_2')),
            'expresspay' => ! empty(config('payments.gateways.expresspay.token')),
            default => false,
        };
    }

    /**
     * Проверить, включён ли тестовый режим для шлюза
     */
    public function isGatewayTestMode(string $gateway): bool
    {
        $setting = PaymentGatewaySetting::findByGateway($gateway);

        if ($setting && $setting->test_mode !== null) {
            return $setting->test_mode;
        }

        return (bool) config("payments.gateways.{$gateway}.test_mode", true);
    }

    /**
     * Получить все включённые шлюзы
     */
    public function getEnabledGateways(): Collection
    {
        return $this->getAllGatewaysInfo()
            ->filter(fn ($g) => $g['enabled'])
            ->sortBy('priority');
    }

    /**
     * Получить информацию о всех шлюзах
     */
    public function getAllGatewaysInfo(): Collection
    {
        $configGateways = config('payments.gateways', []);
        $dbSettings = PaymentGatewaySetting::all()->keyBy('gateway');

        return collect($configGateways)->map(function ($config, $name) use ($dbSettings) {
            $dbSetting = $dbSettings->get($name);

            return [
                'name' => $name,
                'display_name' => $config['display_name'] ?? $name,
                'available' => $config['available'] ?? true,
                'configured' => $this->isGatewayConfigured($name),
                'enabled' => $this->isGatewayEnabled($name),
                'test_mode' => $this->isGatewayTestMode($name),
                'priority' => $dbSetting?->priority ?? 0,
                'currencies' => $config['currencies'] ?? [],
                'supports_refund' => $config['supports_refund'] ?? false,
                'supports_widget' => $config['supports_widget'] ?? false,
            ];
        });
    }

    /**
     * Обновить настройки шлюза
     */
    public function updateGatewaySettings(string $gateway, array $data): PaymentGatewaySetting
    {
        return PaymentGatewaySetting::updateOrCreate(
            ['gateway' => $gateway],
            array_filter([
                'enabled' => $data['enabled'] ?? null,
                'test_mode' => $data['test_mode'] ?? null,
                'priority' => $data['priority'] ?? null,
                'config' => $data['config'] ?? null,
            ], fn ($v) => $v !== null)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Типы оплат
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить, включён ли тип оплаты
     */
    public function isTypeEnabled(string $type): bool
    {
        // 1. Проверяем доступность в конфиге
        if (! config("payments.types.{$type}.available", true)) {
            return false;
        }

        // 2. Проверяем настройку в БД
        $setting = PaymentTypeSetting::findByType($type);

        if ($setting) {
            return $setting->enabled;
        }

        // 3. По умолчанию выключен
        return false;
    }

    /**
     * Получить шлюз по умолчанию для типа оплаты
     */
    public function getDefaultGatewayForType(string $type): ?string
    {
        // 1. Проверяем настройку в БД
        $setting = PaymentTypeSetting::findByType($type);

        if ($setting && $setting->default_gateway && $this->isGatewayEnabled($setting->default_gateway)) {
            return $setting->default_gateway;
        }

        // 2. Берём первый включённый шлюз из разрешённых для этого типа
        $allowedGateways = $setting?->allowed_gateways ?? [];

        foreach ($allowedGateways as $gateway) {
            if ($this->isGatewayEnabled($gateway)) {
                return $gateway;
            }
        }

        // 3. Берём любой включённый шлюз
        $enabledGateways = $this->getEnabledGateways();
        if ($enabledGateways->isNotEmpty()) {
            return $enabledGateways->first()['name'];
        }

        return null;
    }

    /**
     * Получить разрешённые шлюзы для типа оплаты (только включённые)
     */
    public function getAllowedGatewaysForType(string $type): array
    {
        $setting = PaymentTypeSetting::findByType($type);

        $allowedGateways = $setting?->allowed_gateways ?? [];

        // Фильтруем только включённые шлюзы
        return array_values(array_filter(
            $allowedGateways,
            fn ($g) => $this->isGatewayEnabled($g)
        ));
    }

    /**
     * Получить все включённые типы оплат
     */
    public function getEnabledTypes(): Collection
    {
        return $this->getAllTypesInfo()->filter(fn ($t) => $t['enabled']);
    }

    /**
     * Получить информацию о всех типах оплат
     */
    public function getAllTypesInfo(): Collection
    {
        $configTypes = config('payments.types', []);
        $dbSettings = PaymentTypeSetting::all()->keyBy('type');

        return collect($configTypes)->map(function ($config, $name) use ($dbSettings) {
            $dbSetting = $dbSettings->get($name);

            // Берём только из БД, без fallback на конфиг
            $allowedGateways = $dbSetting?->allowed_gateways ?? [];

            // Фильтруем только включённые шлюзы
            $activeGateways = array_filter(
                $allowedGateways,
                fn ($g) => $this->isGatewayEnabled($g)
            );

            return [
                'name' => $name,
                'display_name' => $config['name'] ?? $name,
                'description' => $config['description'] ?? '',
                'available' => $config['available'] ?? true,
                'enabled' => $this->isTypeEnabled($name),
                'allowed_gateways' => $allowedGateways,
                'active_gateways' => array_values($activeGateways),
                'default_gateway' => $dbSetting?->default_gateway ?? $activeGateways[0] ?? null,
                'min_amount' => $dbSetting?->min_amount,
                'max_amount' => $dbSetting?->max_amount,
            ];
        });
    }

    /**
     * Обновить настройки типа оплаты
     */
    public function updateTypeSettings(string $type, array $data): PaymentTypeSetting
    {
        return PaymentTypeSetting::updateOrCreate(
            ['type' => $type],
            array_filter([
                'enabled' => $data['enabled'] ?? null,
                'default_gateway' => $data['default_gateway'] ?? null,
                'allowed_gateways' => $data['allowed_gateways'] ?? null,
                'min_amount' => $data['min_amount'] ?? null,
                'max_amount' => $data['max_amount'] ?? null,
                'config' => $data['config'] ?? null,
            ], fn ($v) => $v !== null)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Общие методы
    |--------------------------------------------------------------------------
    */

    /**
     * Получить все настройки для отображения в админке
     */
    public function getAllSettings(): array
    {
        return [
            'gateways' => $this->getAllGatewaysInfo()->toArray(),
            'types' => $this->getAllTypesInfo()->toArray(),
        ];
    }

    /**
     * Инициализировать настройки по умолчанию в БД
     *
     * Вызывается при первом запуске или через artisan команду
     */
    public function initializeDefaults(): void
    {
        // Собираем все доступные шлюзы
        $availableGateways = collect(config('payments.gateways', []))
            ->filter(fn ($config) => $config['available'] ?? true)
            ->keys()
            ->toArray();

        // Создаём записи для шлюзов
        foreach (config('payments.gateways', []) as $name => $config) {
            PaymentGatewaySetting::firstOrCreate(
                ['gateway' => $name],
                [
                    'enabled' => false,
                    'priority' => 0,
                ]
            );
        }

        // Создаём записи для типов оплат
        // При инициализации разрешаем все доступные шлюзы
        foreach (config('payments.types', []) as $name => $config) {
            PaymentTypeSetting::firstOrCreate(
                ['type' => $name],
                [
                    'enabled' => $name === 'subscription', // Подписки включены по умолчанию
                    'allowed_gateways' => $availableGateways,
                ]
            );
        }
    }
}
