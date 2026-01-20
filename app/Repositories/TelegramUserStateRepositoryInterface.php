<?php

namespace App\Repositories;

use App\Models\TelegramUserState;

/**
 * Интерфейс для репозитория TelegramUserState
 */
interface TelegramUserStateRepositoryInterface extends RepositoryInterface
{
    /**
     * Получить текущее состояние пользователя
     */
    public function getCurrentState(string $telegramUserId): ?TelegramUserState;

    /**
     * Получить состояние для конкретного бизнеса
     */
    public function getState(string $telegramUserId, ?int $businessId): ?TelegramUserState;

    /**
     * Обновить или создать состояние
     */
    public function updateState(string $telegramUserId, ?int $businessId, string $step, array $data = []): TelegramUserState;

    /**
     * Обновить состояние, сохраняя message_id
     */
    public function updateStateKeepMessageId(string $telegramUserId, ?int $businessId, string $step, array $data = []): TelegramUserState;

    /**
     * Очистить состояние пользователя
     */
    public function clearState(string $telegramUserId, ?int $businessId = null): bool;

    /**
     * Сохранить ID последнего сообщения
     */
    public function setMessageId(string $telegramUserId, ?int $businessId, int $messageId): void;

    /**
     * Получить ID последнего сообщения
     */
    public function getMessageId(string $telegramUserId, ?int $businessId): ?int;
}
