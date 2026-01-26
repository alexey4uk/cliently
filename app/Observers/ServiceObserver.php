<?php

namespace App\Observers;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class ServiceObserver
{
    /**
     * Handle the Service "saved" event.
     */
    public function saved(Service $service): void
    {
        $this->clearServiceCache($service);
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
        $this->clearServiceCache($service);
    }

    /**
     * Clear service cache for business.
     */
    protected function clearServiceCache(Service $service): void
    {
        if ($service->business_id) {
            Cache::forget("services_active_business_{$service->business_id}");

            // Также очищаем кеш мастеров, так как они зависят от услуг
            // Очищаем все варианты кеша мастеров для этого бизнеса
            // Это будет сделано через MasterObserver при обновлении связей
        }
    }
}
