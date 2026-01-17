<?php

namespace App\Repositories;

use App\Models\TelegramUserState;

/**
 * Репозиторий для работы с состояниями пользователей Telegram
 */
class TelegramUserStateRepository extends BaseRepository implements TelegramUserStateRepositoryInterface
{
    /**
     * Получить модель для репозитория
     *
     * @return TelegramUserState
     */
    public function getModel(): TelegramUserState
    {
        return new TelegramUserState();
    }

    /**
     * Получить текущее состояние пользователя
     *
     * @param string $telegramUserId
     * @return TelegramUserState|null
     */
    public function getCurrentState(string $telegramUserId): ?TelegramUserState
    {
        return TelegramUserState::getCurrentState($telegramUserId);
    }

    /**
     * Получить состояние для конкретного бизнеса
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @return TelegramUserState|null
     */
    public function getState(string $telegramUserId, ?int $businessId): ?TelegramUserState
    {
        return TelegramUserState::getState($telegramUserId, $businessId);
    }

    /**
     * Обновить или создать состояние
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @param string $step
     * @param array $data
     * @return TelegramUserState
     */
    public function updateState(string $telegramUserId, ?int $businessId, string $step, array $data = []): TelegramUserState
    {
        return TelegramUserState::updateState($telegramUserId, $businessId, $step, $data);
    }

    /**
     * Обновить состояние, сохраняя message_id
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @param string $step
     * @param array $data
     * @return TelegramUserState
     */
    public function updateStateKeepMessageId(string $telegramUserId, ?int $businessId, string $step, array $data = []): TelegramUserState
    {
        return TelegramUserState::updateStateKeepMessageId($telegramUserId, $businessId, $step, $data);
    }

    /**
     * Очистить состояние пользователя
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @return bool
     */
    public function clearState(string $telegramUserId, ?int $businessId = null): bool
    {
        return TelegramUserState::clearState($telegramUserId, $businessId);
    }

    /**
     * Сохранить ID последнего сообщения
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @param int $messageId
     */
    public function setMessageId(string $telegramUserId, ?int $businessId, int $messageId): void
    {
        TelegramUserState::setMessageId($telegramUserId, $businessId, $messageId);
    }

    /**
     * Получить ID последнего сообщения
     *
     * @param string $telegramUserId
     * @param int|null $businessId
     * @return int|null
     */
    public function getMessageId(string $telegramUserId, ?int $businessId): ?int
    {
        return TelegramUserState::getMessageId($telegramUserId, $businessId);
    }
}
