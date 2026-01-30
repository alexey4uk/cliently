<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Cache;

class InvoiceObserver
{
    /**
     * Handle the Invoice "saved" event.
     */
    public function saved(Invoice $invoice): void
    {
        $this->clearAnalyticsCache();
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        $this->clearAnalyticsCache();
    }

    /**
     * Clear analytics cache.
     */
    protected function clearAnalyticsCache(): void
    {
        // Очищаем кеш метрик выручки
        Cache::forget('analytics_revenue_metrics_'.today()->format('Y-m-d'));
        Cache::forget('analytics_invoice_status_stats');

        // Очищаем кеш админской аналитики
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        if ($supportsTags) {
            Cache::tags(['panel_analytics'])->flush();
        }
    }
}
