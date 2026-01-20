<?php

namespace App\Repositories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с бизнесами
 */
class BusinessRepository extends BaseRepository implements BusinessRepositoryInterface
{
    /**
     * Получить модель для репозитория
     *
     * @return Business
     */
    public function getModel(): Business
    {
        return new Business();
    }

    /**
     * Найти бизнес по токену Telegram
     *
     * @param string $token
     * @return Business|null
     */
    public function findByTelegramToken(string $token): ?Business
    {
        return $this->model->where('telegram_token', $token)->first();
    }

    /**
     * Найти бизнес по slug
     *
     * @param string $slug
     * @return Business|null
     */
    public function findBySlug(string $slug): ?Business
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Получить список бизнесов для каталога с пагинацией
     *
     * @param int $page
     * @param int $perPage
     * @return Collection
     */
    public function getPaginated(int $page = 1, int $perPage = 10): Collection
    {
        $offset = ($page - 1) * $perPage;

        return $this->model->select('id', 'name', 'slug')
            ->orderBy('name')
            ->offset($offset)
            ->limit($perPage)
            ->get();
    }

    /**
     * Получить общее количество бизнесов
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->model->count();
    }

    /**
     * Поиск бизнесов по названию с пагинацией
     *
     * @param string $query
     * @param int $page
     * @param int $perPage
     * @return Collection
     */
    public function searchByNamePaginated(string $query, int $page = 1, int $perPage = 10): Collection
    {
        $offset = ($page - 1) * $perPage;

        return $this->model->where('name', 'LIKE', '%' . $query . '%')
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->offset($offset)
            ->limit($perPage)
            ->get();
    }

    /**
     * Получить количество бизнесов по поисковому запросу
     *
     * @param string $query
     * @return int
     */
    public function getSearchCount(string $query): int
    {
        return $this->model->where('name', 'LIKE', '%' . $query . '%')->count();
    }

    /**
     * Обновить чат ID Telegram для бизнеса
     *
     * @param Business $business
     * @param int $chatId
     * @return bool
     */
    public function updateTelegramChatId(Business $business, int $chatId): bool
    {
        return $business->update(['telegram_chat_id' => $chatId]);
    }
}
