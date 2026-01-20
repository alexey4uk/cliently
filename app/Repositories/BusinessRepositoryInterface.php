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
     *
     * @param string $token
     * @return Business|null
     */
    public function findByTelegramToken(string $token): ?Business;

    /**
     * Найти бизнес по slug
     *
     * @param string $slug
     * @return Business|null
     */
    public function findBySlug(string $slug): ?Business;

    /**
     * Получить список бизнесов для каталога с пагинацией
     *
     * @param int $page
     * @param int $perPage
     * @return Collection
     */
    public function getPaginated(int $page = 1, int $perPage = 10): Collection;

    /**
     * Получить общее количество бизнесов
     *
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * Поиск бизнесов по названию с пагинацией
     *
     * @param string $query
     * @param int $page
     * @param int $perPage
     * @return Collection
     */
    public function searchByNamePaginated(string $query, int $page = 1, int $perPage = 10): Collection;

    /**
     * Получить количество бизнесов по поисковому запросу
     *
     * @param string $query
     * @return int
     */
    public function getSearchCount(string $query): int;

    /**
     * Обновить чат ID Telegram для бизнеса
     *
     * @param Business $business
     * @param int $chatId
     * @return bool
     */
    public function updateTelegramChatId(Business $business, int $chatId): bool;
}
