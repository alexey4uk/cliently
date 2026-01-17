<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интерфейс для репозитория Service
 */
interface ServiceRepositoryInterface extends RepositoryInterface
{
    /**
     * Получить активные услуги для локации
     *
     * @param int $locationId
     * @return Collection
     */
    public function getActiveByLocation(int $locationId): Collection;

    /**
     * Получить активные услуги для бизнеса
     *
     * @param int $businessId
     * @return Collection
     */
    public function getActiveByBusiness(int $businessId): Collection;
}
