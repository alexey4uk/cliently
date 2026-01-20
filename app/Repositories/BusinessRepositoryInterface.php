<?php

namespace App\Repositories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интерфейс для репозитория Business
 */
interface BusinessRepositoryInterface extends RepositoryInterface
{
    /**
     * Найти бизнес по токену Telegram
     */
    public function findByTelegramToken(string $token): ?Business;

    /**
     * Найти бизнес по slug
     */
    public function findBySlug(string $slug): ?Business;

    /**
     * Получить список бизнесов для каталога с пагинацией
     */
    public function getPaginated(int $page = 1, int $perPage = 10): Collection;

    /**
     * Получить общее количество бизнесов
     */
    public function getTotalCount(): int;

    /**
     * Поиск бизнесов по названию с пагинацией
     */
    public function searchByNamePaginated(string $query, int $page = 1, int $perPage = 10): Collection;

    /**
     * Получить количество бизнесов по поисковому запросу
     */
    public function getSearchCount(string $query): int;

    /**
     * Обновить чат ID Telegram для бизнеса
     */
    public function updateTelegramChatId(Business $business, int $chatId): bool;
}
