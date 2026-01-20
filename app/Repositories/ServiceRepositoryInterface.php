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
     */
    public function getActiveByLocation(int $locationId): Collection;

    /**
     * Получить активные услуги для бизнеса
     */
    public function getActiveByBusiness(int $businessId): Collection;

    /**
     * Проверить, принадлежит ли услуга бизнесу
     */
    public function belongsToBusiness(int $serviceId, int $businessId): bool;
}
