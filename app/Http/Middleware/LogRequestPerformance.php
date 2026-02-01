<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PerformanceMonitor;

class LogRequestPerformance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $monitor = app('performance.monitor');
        $monitor->start();

        $response = $next($request);

        // Логируем производительность только для определенных маршрутов
        if ($this->shouldLog($request)) {
            $stats = $monitor->getStats();
            
            Log::channel('performance')->info('Request performance', [
                'method' => $request->method(),
                'url' => $request->url(),
                'route' => $request->route()?->getName(),
                'user_id' => $request->user()?->id,
                'stats' => $stats,
            ]);
        }

        return $response;
    }

    /**
     * Определяем, нужно ли логировать этот запрос
     */
    private function shouldLog(Request $request): bool
    {
        // Логируем только определенные маршруты
        $logRoutes = [
            'dashboard',
            'panel.analytics.*',
            'analytics.*',
            'appointments.*',
            'clients.*',
            'settings.*',
        ];

        $routeName = $request->route()?->getName();
        
        if (!$routeName) {
            return false;
        }

        // Проверяем соответствие маршрутам
        foreach ($logRoutes as $pattern) {
            if (str_ends_with($pattern, '*')) {
                $prefix = rtrim($pattern, '*');
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }
}