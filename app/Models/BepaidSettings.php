<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BepaidSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_mode',
        'test_shop_id',
        'test_secret_key',
        'test_gateway_base',
        'test_checkout_base',
        'production_shop_id',
        'production_secret_key',
        'production_gateway_base',
        'production_checkout_base',
        'webhook_url',
        'enabled',
    ];

    protected $casts = [
        'test_mode' => 'boolean',
        'enabled' => 'boolean',
    ];

    /**
     * Получить или создать единственную запись настроек (singleton)
     * 
     * Использует кеш Laravel для оптимизации запросов к БД.
     * Кеш автоматически сбрасывается при обновлении настроек через Observer.
     * 
     * ВАЖНО: Если настройки обновляются напрямую через DB::table() (например, в тестах),
     * используйте clearCache() для сброса кеша.
     */
    public static function getSettings(): self
    {
        return Cache::remember('bepaid_settings', 3600, function () {
            return self::firstOrCreate(
                ['id' => 1],
                [
                    'test_mode' => true,
                    'enabled' => false,
                ]
            );
        });
    }

    /**
     * Очистить кеш настроек
     */
    public static function clearCache(): void
    {
        Cache::forget('bepaid_settings');
    }

    /**
     * Получить текущие настройки для использования
     *
     * ВАЖНО: Эти shop_id и secret_key используются для:
     *
     * 1. API запросов к bePaid:
     *    - Создание платежных токенов (BepaidService::createPaymentToken)
     *    - Проверка статуса платежа (BepaidService::checkPaymentStatus)
     *    - Возврат средств (BepaidService::refund)
     *
     * 2. Проверки webhook от bePaid:
     *    - В BepaidWebhookController::validateBasicAuth
     *    - bePaid отправляет webhook с заголовком: Authorization: Basic base64(shop_id:secret_key)
     *    - Мы сравниваем shop_id и secret_key из заголовка с настройками из БД
     *    - Если совпадают - обрабатываем webhook, если нет - возвращаем 401
     *
     * ВАЖНО: shop_id и secret_key должны совпадать с настройками магазина в системе bePaid!
     * bePaid использует эти же credentials для формирования Basic Auth в webhook.
     *
     * @return array Массив с настройками: shop_id, secret_key, gateway_base, checkout_base
     */
    public function getCurrentSettings(): array
    {
        // Если включен тестовый режим - используем тестовые настройки
        // Иначе - используем продакшн настройки
        if ($this->test_mode) {
            return [
                'shop_id' => $this->test_shop_id,
                'secret_key' => $this->test_secret_key,
                'gateway_base' => $this->test_gateway_base ?? config('bepaid.test.gateway_base'),
                'checkout_base' => $this->test_checkout_base ?? config('bepaid.test.checkout_base'),
            ];
        }

        return [
            'shop_id' => $this->production_shop_id,
            'secret_key' => $this->production_secret_key,
            'gateway_base' => $this->production_gateway_base ?? config('bepaid.production.gateway_base'),
            'checkout_base' => $this->production_checkout_base ?? config('bepaid.production.checkout_base'),
        ];
    }
}
