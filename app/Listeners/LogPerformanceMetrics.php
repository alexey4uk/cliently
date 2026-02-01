<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Events\QueryExecuted;

class LogPerformanceMetrics
{
    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event): void
    {
        // Логируем только медленные запросы (>100ms)
        if ($event->time > 100) {
            Log::channel('performance')->warning('Slow query detected', [
                'sql' => $event->sql,
                'bindings' => $event->bindings,
                'time' => $event->time . 'ms',
                'connection' => $event->connectionName,
            ]);
        }
        
        // Статистика по запросам
        Log::channel('performance')->debug('Query executed', [
            'sql' => substr($event->sql, 0, 100),
            'time' => $event->time . 'ms',
            'rows' => $event->rowCount ?? 0,
        ]);
    }
}