<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с услугами
 */
class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    /**
     * Получить модель для репозитория
     *
     * @return Service
     */
    public function getModel(): Service
    {
        return new Service();
    }

    /**
     * Получить активные услуги для локации
     *
     * @param int $locationId
     * @return Collection
     */
    public function getActiveByLocation(int $locationId): Collection
    {
        return $this->model->whereHas('locations', function ($q) use ($locationId) {
            $q->where('locations.id', $locationId);
        })->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Получить активные услуги для бизнеса
     *
     * @param int $businessId
     * @return Collection
     */
    public function getActiveByBusiness(int $businessId): Collection
    {
        return $this->model->where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Проверить, принадлежит ли услуга бизнесу
     *
     * @param int $serviceId
     * @param int $businessId
     * @return bool
     */
    public function belongsToBusiness(int $serviceId, int $businessId): bool
    {
        return $this->model->where('id', $serviceId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
