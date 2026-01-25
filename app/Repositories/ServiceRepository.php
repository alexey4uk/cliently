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
     */
    public function getModel(): Service
    {
        return new Service;
    }

    /**
     * Получить активные услуги для бизнеса
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
     */
    public function belongsToBusiness(int $serviceId, int $businessId): bool
    {
        return $this->model->where('id', $serviceId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
