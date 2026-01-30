<?php

namespace App\Repositories;

use App\Models\Master;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интерфейс для репозитория Master
 */
interface MasterRepositoryInterface extends RepositoryInterface
{
    /**
     * Получить активных мастеров для локации
     */
    public function getActiveByLocation(int $locationId, ?int $serviceId = null): Collection;

    /**
     * Получить активных мастеров для бизнеса
     */
    public function getActiveByBusiness(int $businessId, ?int $serviceId = null): Collection;

    /**
     * Получить мастеров, которые предоставляют услугу
     */
    public function getByService(int $serviceId): Collection;

    /**
     * Проверить, принадлежит ли мастер бизнесу
     */
    public function belongsToBusiness(int $masterId, int $businessId): bool;
}
