<?php

namespace App\Observers;

use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class LocationObserver
{
    /**
     * Handle the Location "saved" event.
     */
    public function saved(Location $location): void
    {
        $this->clearLocationCache($location);
    }

    /**
     * Handle the Location "deleted" event.
     */
    public function deleted(Location $location): void
    {
        $this->clearLocationCache($location);
    }

    /**
     * Clear location cache for business.
     */
    protected function clearLocationCache(Location $location): void
    {
        if ($location->business_id) {
            Cache::forget("locations_business_{$location->business_id}");
            
            // Также очищаем кеш мастеров для этой локации
            // Очищаем все варианты кеша мастеров для этой локации
            // Это будет сделано через MasterObserver при обновлении связей
        }
    }
}
