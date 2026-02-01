<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
/**
 * Репозиторий для работы с локациями
 */
class LocationRepository extends BaseRepository implements LocationRepositoryInterface
{
    /**
     * Получить модель для репозитория
     */
    public function getModel(): Location
    {
        return new Location;
    }

    /**
     * Получить локации для бизнеса
     */
    public function getByBusiness(int $businessId): Collection
    {
        return $this->model->where('business_id', $businessId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Проверить, принадлежит ли локация бизнесу
     */
    public function belongsToBusiness(int $locationId, int $businessId): bool
    {
        return $this->model->where('id', $locationId)
            ->where('business_id', $businessId)
            ->exists();
    }
}
