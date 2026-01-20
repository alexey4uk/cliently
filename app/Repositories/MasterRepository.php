<?php

namespace App\Repositories;

use App\Models\Master;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с мастерами
 */
class MasterRepository extends BaseRepository implements MasterRepositoryInterface
{
    /**
     * Получить модель для репозитория
     */
    public function getModel(): Master
    {
        return new Master;
    }

    /**
     * Получить активных мастеров для локации
     */
    public function getActiveByLocation(int $locationId, ?int $serviceId = null): Collection
    {
        $query = $this->model->whereHas('locations', function ($q) use ($locationId) {
            $q->where('locations.id', $locationId);
        })->where('is_active', true);

        if ($serviceId) {
            $query->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            });
        }

        return $query->orderBy('first_name')->get();
    }

    /**
     * Получить активных мастеров для бизнеса
     */
    public function getActiveByBusiness(int $businessId, ?int $serviceId = null): Collection
    {
        $query = $this->model->where('business_id', $businessId)->where('is_active', true);

        if ($serviceId) {
            $query->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            });
        }

        return $query->orderBy('first_name')->get();
    }

    /**
     * Получить мастеров, которые предоставляют услугу
     */
    public function getByService(int $serviceId): Collection
    {
        return $this->model->whereHas('services', function ($q) use ($serviceId) {
            $q->where('services.id', $serviceId);
        })->where('is_active', true)->orderBy('first_name')->get();
    }

    /**
     * Проверить, принадлежит ли мастер бизнесу
     */
    public function belongsToBusiness(int $masterId, int $businessId): bool
    {
        return $this->model->where('id', $masterId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
