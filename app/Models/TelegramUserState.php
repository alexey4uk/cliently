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
        'last_message_id',
    ];

    protected $casts = [
        'data' => 'array',
        'last_message_id' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Получить состояние пользователя для конкретного бизнеса
     */
    public static function getState(string $telegramUserId, ?int $businessId): ?self
    {
        return self::where('telegram_user_id', $telegramUserId)
            ->where('business_id', $businessId)
            ->first();
    }

    /**
     * Создать или обновить состояние
     */
    public static function updateState(string $telegramUserId, ?int $businessId, string $step, array $data = []): self
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
     * Обновить состояние, сохраняя last_message_id
     * Если $businessId равен null, используется отдельное состояние без привязки к бизнесу
     */
    public static function updateStateKeepMessageId(string $telegramUserId, ?int $businessId, string $step, array $data = []): self
    {
        $state = self::where('telegram_user_id', $telegramUserId)
            ->where('business_id', $businessId)
            ->first();
            
        if ($state) {
            $state->update([
                'step' => $step,
                'data' => $data,
            ]);
            return $state;
        }
        
        return self::create([
            'telegram_user_id' => $telegramUserId,
            'business_id' => $businessId,
            'step' => $step,
            'data' => $data,
        ]);
    }

    /**
     * Очистить состояние пользователя
     * Если $businessId равен null, очищает все состояния пользователя
     */
    public static function clearState(string $telegramUserId, ?int $businessId = null): bool
    {
        $query = self::where('telegram_user_id', $telegramUserId);
        
        if ($businessId !== null) {
            $query->where('business_id', $businessId);
        }
        
        return $query->delete() > 0;
    }

    /**
     * Сохранить ID последнего сообщения бота
     */
    public static function setMessageId(string $telegramUserId, ?int $businessId, int $messageId): void
    {
        self::where('telegram_user_id', $telegramUserId)
            ->where('business_id', $businessId)
            ->update(['last_message_id' => $messageId]);
    }

    /**
     * Получить ID последнего сообщения бота
     */
    public static function getMessageId(string $telegramUserId, ?int $businessId): ?int
    {
        $state = self::where('telegram_user_id', $telegramUserId)
            ->where('business_id', $businessId)
            ->first();
        return $state?->last_message_id;
    }

    /**
     * Обновить состояние с сохранением message_id
     */
    public static function updateStateWithMessageId(string $telegramUserId, ?int $businessId, string $step, array $data = [], ?int $messageId = null): self
    {
        return self::updateOrCreate(
            [
                'telegram_user_id' => $telegramUserId,
                'business_id' => $businessId,
            ],
            [
                'step' => $step,
                'data' => $data,
                'last_message_id' => $messageId,
            ]
        );
    }
}
