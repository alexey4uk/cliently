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
     *
     * @param int $locationId
     * @param int|null $serviceId
     * @return Collection
     */
    public function getActiveByLocation(int $locationId, ?int $serviceId = null): Collection;

    /**
     * Получить активных мастеров для бизнеса
     *
     * @param int $businessId
     * @param int|null $serviceId
     * @return Collection
     */
    public function getActiveByBusiness(int $businessId, ?int $serviceId = null): Collection;

    /**
     * Получить мастеров, которые предоставляют услугу
     *
     * @param int $serviceId
     * @return Collection
     */
    public function getByService(int $serviceId): Collection;
}
