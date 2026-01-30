<?php

namespace App\Observers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Cache;

class AppointmentObserver
{
    /**
     * Handle the Appointment "saved" event.
     */
    public function saved(Appointment $appointment): void
    {
        $this->clearAnalyticsCache($appointment);
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $this->clearAnalyticsCache($appointment);
    }

    /**
     * Clear analytics cache for business.
     */
    protected function clearAnalyticsCache(Appointment $appointment): void
    {
        if ($appointment->business_id) {
            // Очищаем кеш клиентской аналитики
            $supportsTags = method_exists(Cache::getStore(), 'tags');
            if ($supportsTags) {
                Cache::tags(['analytics', "business_{$appointment->business_id}"])->flush();
            } else {
                // Очищаем основные ключи вручную
                Cache::forget("analytics_kpi_{$appointment->business_id}");
                // Остальные ключи будут очищены при следующем запросе
            }

            // Очищаем кеш админской аналитики (все пользователи)
            if ($supportsTags) {
                Cache::tags(['panel_analytics'])->flush();
            }
        }
    }
}
