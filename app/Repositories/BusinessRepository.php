<?php

namespace App\Repositories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

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
     * Найти бизнес по ID (с кешированием)
     * Кэш на 10 минут для баланса между производительностью и актуальностью.
     */
    public function findById(int $id): ?Business
    {
        return Cache::remember("business_{$id}", 600, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * Найти бизнес по токену Telegram
     * Кэш на 10 минут для баланса между производительностью и актуальностью.
     */
    public function findByTelegramToken(string $token): ?Business
    {
        return Cache::remember("business_telegram_token_{$token}", 600, function () use ($token) {
            return $this->model->where('telegram_token', $token)->first();
        });
    }

    /**
     * Найти бизнес по slug
     * Кэш на 10 минут для баланса между производительностью и актуальностью.
     */
    public function findBySlug(string $slug): ?Business
    {
        return Cache::remember("business_slug_{$slug}", 600, function () use ($slug) {
            return $this->model->where('slug', $slug)->first();
        });
    }

    /**
     * Получить список бизнесов для каталога с пагинацией
     * Кэш на 10 минут для баланса между производительностью и актуальностью.
     */
    public function getPaginated(int $page = 1, int $perPage = 10): Collection
    {
        $cacheKey = "businesses_paginated_{$page}_{$perPage}";

        return Cache::remember($cacheKey, 600, function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;

            return $this->model->select('id', 'name', 'slug')
                ->orderBy('name')
                ->offset($offset)
                ->limit($perPage)
                ->get();
        });
    }

    /**
     * Получить общее количество бизнесов
     * Кэш на 10 минут для баланса между производительностью и актуальностью.
     */
    public function getTotalCount(): int
    {
        return Cache::remember('businesses_total_count', 600, function () {
            return $this->model->count();
        });
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
     * Получить список всех бизнесов для фильтров
     * Кэш на 10 минут для баланса между производительностью и актуальностью.
     */
    public function getAllForFilter(): Collection
    {
        return Cache::remember('businesses_list_filter', 600, function () {
            return $this->model->orderBy('name')->get();
        });
    }

    /**
     * Обновить чат ID Telegram для бизнеса
     */
    public function updateTelegramChatId(Business $business, int $chatId): bool
    {
        return $business->update(['telegram_chat_id' => $chatId]);
    }

    /**
     * Clear business cache.
     * Call this method when business data is changed.
     *
     * @param  Business|null  $business  Business instance or null to clear all
     */
    public function clearCache(?Business $business = null): void
    {
        if ($business) {
            Cache::forget("business_{$business->id}");
            Cache::forget("business_slug_{$business->slug}");
            if ($business->telegram_token) {
                Cache::forget("business_telegram_token_{$business->telegram_token}");
            }
        }

        // Очищаем общие кеши
        Cache::forget('businesses_total_count');
        Cache::forget('businesses_list_filter');
        // Очищаем все страницы пагинации (можно использовать теги кеша в будущем)
        // Для простоты очищаем первые 10 страниц
        for ($page = 1; $page <= 10; $page++) {
            for ($perPage = 10; $perPage <= 50; $perPage += 10) {
                Cache::forget("businesses_paginated_{$page}_{$perPage}");
            }
        }
    }
}
