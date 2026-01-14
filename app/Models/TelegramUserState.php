<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramUserState extends Model
{
    protected $fillable = [
        'telegram_user_id',
        'step',
        'data',
        'business_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Получить состояние пользователя для конкретного бизнеса
     */
    public static function getState(string $telegramUserId, int $businessId): ?self
    {
        return self::where('telegram_user_id', $telegramUserId)
            ->where('business_id', $businessId)
            ->first();
    }

    /**
     * Создать или обновить состояние
     */
    public static function updateState(string $telegramUserId, int $businessId, string $step, array $data = []): self
    {
        return self::updateOrCreate(
            [
                'telegram_user_id' => $telegramUserId,
                'business_id' => $businessId,
            ],
            [
                'step' => $step,
                'data' => $data,
            ]
        );
    }

    /**
     * Очистить состояние пользователя
     */
    public static function clearState(string $telegramUserId, int $businessId): bool
    {
        return self::where('telegram_user_id', $telegramUserId)
            ->where('business_id', $businessId)
            ->delete() > 0;
    }
}
