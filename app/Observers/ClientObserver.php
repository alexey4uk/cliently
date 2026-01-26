<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Cache;

class ClientObserver
{
    /**
     * Handle the Client "saved" event.
     */
    public function saved(Client $client): void
    {
        $this->clearClientCache($client);
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        $this->clearClientCache($client);
    }

    /**
     * Clear client cache for dashboard.
     */
    protected function clearClientCache(Client $client): void
    {
        if ($client->business_id) {
            // Очищаем кеш для всех возможных лимитов (обычно используется 5)
            for ($limit = 5; $limit <= 20; $limit += 5) {
                Cache::forget("clients_recent_dashboard_{$client->business_id}_{$limit}");
            }
        }
    }
}
