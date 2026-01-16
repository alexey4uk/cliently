<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramUserState extends Model
{
    use HasFactory;

    // Константы для шагов
    public const STEP_START = 'start';

    public const STEP_SEARCH = 'search';

    public const STEP_SELECT_LOCATION = 'select_location';

    public const STEP_SELECT_SERVICE = 'select_service';

    public const STEP_SELECT_MASTER = 'select_master';

    public const STEP_SELECT_DATE = 'select_date';

    public const STEP_SELECT_TIME = 'select_time';

    public const STEP_ENTER_CLIENT_INFO = 'enter_client_info';

    public const STEP_ENTER_PHONE = 'enter_phone';

    public const STEP_ENTER_NOTES = 'enter_notes';

    public const STEP_CONFIRM_APPOINTMENT = 'confirm_appointment';

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
     * Получить текущее состояние пользователя
     * Сначала ищет состояние с бизнесом, если не найдено - первое состояние
     */
    public static function getCurrentState(string $telegramUserId): ?self
    {
        // Сначала пытаемся найти состояние с business_id
        $state = self::where('telegram_user_id', $telegramUserId)
            ->whereNotNull('business_id')
            ->first();

        // Если не найдено, используем любое состояние (может быть поиск)
        if (! $state) {
            $state = self::where('telegram_user_id', $telegramUserId)->first();
        }

        return $state;
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
