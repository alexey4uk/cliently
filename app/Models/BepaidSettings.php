<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => false,
            ]
        );
    }

    /**
     * Получить текущие настройки для использования
     */
    public function getCurrentSettings(): array
    {
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
