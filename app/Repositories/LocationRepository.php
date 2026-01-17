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
     *
     * @return Location
     */
    public function getModel(): Location
    {
        return new Location();
    }

    /**
     * Получить локации для бизнеса
     *
     * @param int $businessId
     * @return Collection
     */
    public function getByBusiness(int $businessId): Collection
    {
        return $this->model->where('business_id', $businessId)->orderBy('name')->get();
    }
}
