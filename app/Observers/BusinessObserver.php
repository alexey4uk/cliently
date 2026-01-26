<?php

namespace App\Observers;

use App\Models\Business;
use App\Repositories\BusinessRepository;
use Illuminate\Support\Facades\Cache;

class BusinessObserver
{
    /**
     * Handle the Business "saved" event.
     */
    public function saved(Business $business): void
    {
        $this->clearBusinessCache($business);
    }

    /**
     * Handle the Business "deleted" event.
     */
    public function deleted(Business $business): void
    {
        $this->clearBusinessCache($business);
    }

    /**
     * Clear business cache.
     */
    protected function clearBusinessCache(Business $business): void
    {
        $repository = app(BusinessRepository::class);
        $repository->clearCache($business);
    }
}
