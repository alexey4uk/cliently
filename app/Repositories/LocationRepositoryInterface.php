<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интерфейс для репозитория Location
 */
interface LocationRepositoryInterface extends RepositoryInterface
{
    /**
     * Получить локации для бизнеса
     *
     * @param int $businessId
     * @return Collection
     */
    public function getByBusiness(int $businessId): Collection;
}
