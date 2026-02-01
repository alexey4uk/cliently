<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;
use App\Listeners\LogPerformanceMetrics;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('performance.monitor', function () {
            return new class {
                private $queryCount = 0;
                private $queryTime = 0;
                private $cacheHits = 0;
                private $cacheMisses = 0;
                private $startTime = null;

                public function start(): void
                {
                    $this->startTime = microtime(true);
                    $this->reset();
                }

                public function reset(): void
                {
                    $this->queryCount = 0;
                    $this->queryTime = 0;
                    $this->cacheHits = 0;
                    $this->cacheMisses = 0;
                }

                public function addQuery($time): void
                {
                    $this->queryCount++;
                    $this->queryTime += $time;
                }

                public function addCacheHit(): void
                {
                    $this->cacheHits++;
                }

                public function addCacheMiss(): void
                {
                    $this->cacheMisses++;
                }

                public function getStats(): array
                {
                    $memory = memory_get_usage();
                    $peakMemory = memory_get_peak_usage();
                    $executionTime = microtime(true) - ($this->startTime ?? microtime(true));

                    return [
                        'queries' => [
                            'count' => $this->queryCount,
                            'time_total' => round($this->queryTime, 2) . 'ms',
                            'time_avg' => $this->queryCount > 0 ? round($this->queryTime / $this->queryCount, 2) . 'ms' : '0ms',
                        ],
                        'cache' => [
                            'hits' => $this->cacheHits,
                            'misses' => $this->cacheMisses,
                            'hit_rate' => $this->cacheHits + $this->cacheMisses > 0 
                                ? round(($this->cacheHits / ($this->cacheHits + $this->cacheMisses)) * 100, 1) . '%' 
                                : '0%',
                        ],
                        'memory' => [
                            'current' => $this->formatBytes($memory),
                            'peak' => $this->formatBytes($peakMemory),
                        ],
                        'execution_time' => round($executionTime, 3) . 's',
                    ];
                }

                private function formatBytes($bytes): string
                {
                    if ($bytes >= 1073741824) {
                        return round($bytes / 1073741824, 2) . ' GB';
                    } elseif ($bytes >= 1048576) {
                        return round($bytes / 1048576, 2) . ' MB';
                    } elseif ($bytes >= 1024) {
                        return round($bytes / 1024, 2) . ' KB';
                    } else {
                        return $bytes . ' bytes';
                    }
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Подписываемся на события запросов
        Event::listen(QueryExecuted::class, function ($event) {
            $monitor = app('performance.monitor');
            $monitor->addQuery($event->time);
        });

        // Подписываемся на события кеша
        $this->app->resolving('cache', function ($cache) {
            $store = $cache->getStore();
            
            // Оборачиваем store для подсчета обращений
            $originalGet = $store->get ?? null;
            if ($originalGet) {
                $store->get = function ($key, $default = null, $ttl = null) use ($originalGet, $store) {
                    $result = $originalGet($key, $default, $ttl);
                    if ($result !== $default) {
                        app('performance.monitor')->addCacheHit();
                    } else {
                        app('performance.monitor')->addCacheMiss();
                    }
                    return $result;
                };
            }
        });
    }
}