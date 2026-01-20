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
     */
    public function getModel(): Business
    {
        return new Business;
    }

    /**
     * Найти бизнес по токену Telegram
     */
    public function findByTelegramToken(string $token): ?Business
    {
        return $this->model->where('telegram_token', $token)->first();
    }

    /**
     * Найти бизнес по slug
     */
    public function findBySlug(string $slug): ?Business
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Получить список бизнесов для каталога с пагинацией
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
     */
    public function getTotalCount(): int
    {
        return $this->model->count();
    }

    /**
     * Поиск бизнесов по названию с пагинацией
     */
    public function searchByNamePaginated(string $query, int $page = 1, int $perPage = 10): Collection
    {
        $offset = ($page - 1) * $perPage;

        return $this->model->where('name', 'LIKE', '%'.$query.'%')
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->offset($offset)
            ->limit($perPage)
            ->get();
    }

    /**
     * Получить количество бизнесов по поисковому запросу
     */
    public function getSearchCount(string $query): int
    {
        return $this->model->where('name', 'LIKE', '%'.$query.'%')->count();
    }

    /**
     * Обновить чат ID Telegram для бизнеса
     */
    public function updateTelegramChatId(Business $business, int $chatId): bool
    {
        return $business->update(['telegram_chat_id' => $chatId]);
    }
}
