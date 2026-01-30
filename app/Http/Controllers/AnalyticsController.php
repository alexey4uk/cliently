<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionAccessService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Добро пожаловать! Сначала создайте свой бизнес или примите приглашение.');
        }

        // Проверяем доступ к аналитике (проверяет подписку владельца бизнеса)
        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );

        if ($redirect) {
            return $redirect;
        }

        $kpiData = $this->getKPIData($business->id);

        return view('analytics.index', [
            'business' => $business,
            'kpiData' => $kpiData,
        ]);
    }

    public function financial(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Добро пожаловать! Сначала создайте свой бизнес или примите приглашение.');
        }

        // Проверяем доступ к аналитике (проверяет подписку владельца бизнеса)
        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );

        if ($redirect) {
            return $redirect;
        }

        // Проверяем доступ к расширенной аналитике
        $hasAdvancedAnalytics = $this->hasAdvancedAnalytics($business);

        $filters = $this->getFilters($request);
        $data = $this->getFinancialData($business->id, $filters);
        $comparison = $this->getPeriodComparison($business->id, $filters, 'financial');

        return view('analytics.financial', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
            'comparison' => $comparison,
        ]);
    }

    public function general(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Добро пожаловать! Сначала создайте свой бизнес или примите приглашение.');
        }

        // Проверяем доступ к аналитике (проверяет подписку владельца бизнеса)
        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );

        if ($redirect) {
            return $redirect;
        }

        // Проверяем доступ к расширенной аналитике
        $hasAdvancedAnalytics = $this->hasAdvancedAnalytics($business);

        $filters = $this->getFilters($request);
        $data = $this->getGeneralData($business->id, $filters);
        $comparison = $this->getPeriodComparison($business->id, $filters, 'general');
        $timeAnalytics = $this->getTimeAnalytics($business->id, $filters);

        return view('analytics.general', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
            'comparison' => $comparison,
            'timeAnalytics' => $timeAnalytics,
        ]);
    }

    public function clients(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Добро пожаловать! Сначала создайте свой бизнес или примите приглашение.');
        }

        // Проверяем доступ к аналитике (проверяет подписку владельца бизнеса)
        $accessService = app(SubscriptionAccessService::class);
        $redirect = $accessService->checkAccessWithRedirect(
            $business,
            'analytics_enabled',
            'client.analytics.view',
            'Аналитика',
            'subscription.index'
        );

        if ($redirect) {
            return $redirect;
        }

        $filters = $this->getFilters($request);
        $data = $this->getClientsAnalyticsData($business->id, $filters);

        return view('analytics.clients', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
        ]);
    }

    private function getFilters(Request $request): array
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'service_id' => $request->get('service_id'),
            'master_id' => $request->get('master_id'),
            'location_id' => $request->get('location_id'),
        ];
    }

    private function getFinancialData(int $businessId, array $filters): array
    {
        $cacheKey = 'analytics_financial_'.$businessId.'_'.md5(json_encode($filters));
        $cacheTags = ['analytics', "business_{$businessId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($businessId, $filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            $query = \App\Models\Appointment::where('business_id', $businessId)
                ->where('status', 'completed')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($filters['service_id']) {
                $query->where('service_id', $filters['service_id']);
            }
            if ($filters['master_id']) {
                $query->where('master_id', $filters['master_id']);
            }
            if ($filters['location_id']) {
                $query->where('location_id', $filters['location_id']);
            }

            $appointments = $query->with('service')->get();

            // Общая выручка
            $totalRevenue = $appointments->sum(function ($appointment) {
                return $appointment->price ?? $appointment->service->price ?? 0;
            });

            // Количество завершенных записей
            $completedCount = $appointments->count();

            // Средний чек
            $averageCheck = $completedCount > 0 ? round($totalRevenue / $completedCount, 2) : 0;

            // Выручка по периодам (по дням)
            $revenueByPeriod = $this->getRevenueByPeriod($businessId, $startDate, $endDate, $filters);

            // Выручка по услугам
            $revenueByService = $this->getRevenueByService($businessId, $filters);

            // Выручка по мастерам
            $revenueByMaster = $this->getRevenueByMaster($businessId, $filters);

            // Выручка по локациям
            $revenueByLocation = $this->getRevenueByLocation($businessId, $filters);

            return [
                'total_revenue' => $totalRevenue,
                'completed_count' => $completedCount,
                'average_check' => $averageCheck,
                'revenue_by_period' => $revenueByPeriod,
                'revenue_by_service' => $revenueByService,
                'revenue_by_master' => $revenueByMaster,
                'revenue_by_location' => $revenueByLocation,
            ];
        });
    }

    private function getGeneralData(int $businessId, array $filters): array
    {
        $cacheKey = 'analytics_general_'.$businessId.'_'.md5(json_encode($filters));
        $cacheTags = ['analytics', "business_{$businessId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($businessId, $filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            $query = \App\Models\Appointment::where('business_id', $businessId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($filters['service_id']) {
                $query->where('service_id', $filters['service_id']);
            }
            if ($filters['master_id']) {
                $query->where('master_id', $filters['master_id']);
            }
            if ($filters['location_id']) {
                $query->where('location_id', $filters['location_id']);
            }

            $appointments = $query->get();

            // Статистика по статусам
            $statsByStatus = [
                'pending' => $appointments->where('status', 'pending')->count(),
                'confirmed' => $appointments->where('status', 'confirmed')->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
                'cancelled' => $appointments->where('status', 'cancelled')->count(),
            ];

            $total = $appointments->count();
            $completed = $statsByStatus['completed'];
            $cancelled = $statsByStatus['cancelled'];

            // Конверсия и процент отмен
            $conversionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            $cancellationRate = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0;

            // Статистика по периодам
            $statsByPeriod = $this->getAppointmentsStatsByPeriod($businessId, $startDate, $endDate, $filters);

            // Статистика по услугам
            $statsByService = $this->getAppointmentsStatsByService($businessId, $filters);

            // Статистика по мастерам
            $statsByMaster = $this->getAppointmentsStatsByMaster($businessId, $filters);

            return [
                'total' => $total,
                'stats_by_status' => $statsByStatus,
                'conversion_rate' => $conversionRate,
                'cancellation_rate' => $cancellationRate,
                'stats_by_period' => $statsByPeriod,
                'stats_by_service' => $statsByService,
                'stats_by_master' => $statsByMaster,
            ];
        });
    }

    private function getRevenueByPeriod(int $businessId, Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }
        if ($filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }
        if ($filters['location_id']) {
            $query->where('location_id', $filters['location_id']);
        }

        $appointments = $query->with('service')->get();

        // Группируем по датам сразу для оптимизации
        $groupedByDate = $appointments->groupBy(function ($appointment) {
            return $appointment->date->format('Y-m-d');
        });

        $revenueByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayAppointments = $groupedByDate->get($dateStr, collect());

            $dayRevenue = $dayAppointments->sum(function ($appointment) {
                return $appointment->price ?? $appointment->service->price ?? 0;
            });

            $revenueByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'revenue' => $dayRevenue,
                'count' => $dayAppointments->count(),
            ];

            $currentDate->addDay();
        }

        return $revenueByDay;
    }

    private function getRevenueByService(int $businessId, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);

        if ($filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }
        if ($filters['location_id']) {
            $query->where('location_id', $filters['location_id']);
        }

        $appointments = $query->with('service')->get();

        $revenueByService = $appointments->groupBy('service_id')->map(function ($group, $serviceId) {
            $service = $group->first()->service;

            return [
                'service_id' => $serviceId,
                'service_name' => $service ? $service->name : 'Неизвестная услуга',
                'revenue' => $group->sum(function ($appointment) {
                    return $appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0;
                }),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(10)->values();

        return $revenueByService->toArray();
    }

    private function getRevenueByMaster(int $businessId, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);

        if ($filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }
        if ($filters['location_id']) {
            $query->where('location_id', $filters['location_id']);
        }

        $appointments = $query->with(['master', 'service'])->get();

        $revenueByMaster = $appointments->groupBy('master_id')->map(function ($group, $masterId) {
            $master = $group->first()->master;
            $masterName = $master ? trim($master->first_name.' '.($master->last_name ?? '')) : 'Неизвестный мастер';

            return [
                'master_id' => $masterId,
                'master_name' => $masterName,
                'revenue' => $group->sum(function ($appointment) {
                    return $appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0;
                }),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(10)->values();

        return $revenueByMaster->toArray();
    }

    private function getRevenueByLocation(int $businessId, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);

        if ($filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }
        if ($filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }

        $appointments = $query->with(['location', 'service'])->get();

        $revenueByLocation = $appointments->groupBy('location_id')->map(function ($group, $locationId) {
            $location = $group->first()->location;

            return [
                'location_id' => $locationId,
                'location_name' => $location ? $location->name : 'Неизвестная локация',
                'revenue' => $group->sum(function ($appointment) {
                    return $appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0;
                }),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(10)->values();

        return $revenueByLocation->toArray();
    }

    private function getAppointmentsStatsByPeriod(int $businessId, Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }
        if ($filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }
        if ($filters['location_id']) {
            $query->where('location_id', $filters['location_id']);
        }

        $appointments = $query->get();

        $statsByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayAppointments = $appointments->filter(function ($appointment) use ($dateStr) {
                return $appointment->date->format('Y-m-d') === $dateStr;
            });

            $statsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'total' => $dayAppointments->count(),
                'completed' => $dayAppointments->where('status', 'completed')->count(),
                'cancelled' => $dayAppointments->where('status', 'cancelled')->count(),
            ];

            $currentDate->addDay();
        }

        return $statsByDay;
    }

    private function getAppointmentsStatsByService(int $businessId, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);

        if ($filters['master_id']) {
            $query->where('master_id', $filters['master_id']);
        }
        if ($filters['location_id']) {
            $query->where('location_id', $filters['location_id']);
        }

        $appointments = $query->with('service')->get();

        $statsByService = $appointments->groupBy('service_id')->map(function ($group, $serviceId) {
            $service = $group->first()->service;

            return [
                'service_id' => $serviceId,
                'service_name' => $service ? $service->name : 'Неизвестная услуга',
                'total' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'cancelled' => $group->where('status', 'cancelled')->count(),
            ];
        })->values()->sortByDesc('total')->take(10)->values();

        return $statsByService->toArray();
    }

    private function getAppointmentsStatsByMaster(int $businessId, array $filters): array
    {
        $query = \App\Models\Appointment::where('business_id', $businessId)
            ->whereBetween('date', [
                Carbon::parse($filters['date_from'])->format('Y-m-d'),
                Carbon::parse($filters['date_to'])->format('Y-m-d'),
            ]);

        if ($filters['service_id']) {
            $query->where('service_id', $filters['service_id']);
        }
        if ($filters['location_id']) {
            $query->where('location_id', $filters['location_id']);
        }

        $appointments = $query->with('master')->get();

        $statsByMaster = $appointments->groupBy('master_id')->map(function ($group, $masterId) {
            $master = $group->first()->master;
            $masterName = $master ? trim($master->first_name.' '.($master->last_name ?? '')) : 'Неизвестный мастер';

            return [
                'master_id' => $masterId,
                'master_name' => $masterName,
                'total' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'cancelled' => $group->where('status', 'cancelled')->count(),
            ];
        })->values()->sortByDesc('total')->take(10)->values();

        return $statsByMaster->toArray();
    }

    /**
     * Проверить, есть ли доступ к расширенной аналитике
     */
    private function hasAdvancedAnalytics(\App\Models\Business $business): bool
    {
        $subscriptionService = app(SubscriptionService::class);
        $owner = $this->getBusinessOwner($business);

        if (! $owner) {
            return false;
        }

        $limit = $subscriptionService->getLimit($owner, 'advanced_analytics_enabled');

        return $limit === true;
    }

    /**
     * Получить владельца бизнеса
     * Uses cached method for owner role.
     */
    private function getBusinessOwner(\App\Models\Business $business): ?\App\Models\User
    {
        $ownerRole = \App\Models\BusinessRole::getOwnerRole();

        if (! $ownerRole) {
            return null;
        }

        $ownerPivot = \Illuminate\Support\Facades\DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('role_id', $ownerRole->id)
            ->first();

        if (! $ownerPivot) {
            return null;
        }

        return \App\Models\User::find($ownerPivot->user_id);
    }

    /**
     * Получить сравнение текущего периода с предыдущим
     */
    private function getPeriodComparison(int $businessId, array $filters, string $type = 'financial'): array
    {
        $startDate = Carbon::parse($filters['date_from']);
        $endDate = Carbon::parse($filters['date_to']);
        $daysDiff = $startDate->diffInDays($endDate);

        // Вычисляем предыдущий период
        $previousEndDate = $startDate->copy()->subDay();
        $previousStartDate = $previousEndDate->copy()->subDays($daysDiff);

        $previousFilters = [
            'date_from' => $previousStartDate->format('Y-m-d'),
            'date_to' => $previousEndDate->format('Y-m-d'),
            'service_id' => $filters['service_id'],
            'master_id' => $filters['master_id'],
            'location_id' => $filters['location_id'],
        ];

        if ($type === 'financial') {
            $currentData = $this->getFinancialData($businessId, $filters);
            $previousData = $this->getFinancialData($businessId, $previousFilters);

            return [
                'revenue_change' => $currentData['total_revenue'] - $previousData['total_revenue'],
                'revenue_change_percent' => $this->calculatePercentChange(
                    $previousData['total_revenue'],
                    $currentData['total_revenue']
                ),
                'appointments_change' => $currentData['completed_count'] - $previousData['completed_count'],
                'appointments_change_percent' => $this->calculatePercentChange(
                    $previousData['completed_count'],
                    $currentData['completed_count']
                ),
                'average_check_change' => $currentData['average_check'] - $previousData['average_check'],
                'average_check_change_percent' => $this->calculatePercentChange(
                    $previousData['average_check'],
                    $currentData['average_check']
                ),
                'previous_period' => [
                    'total_revenue' => $previousData['total_revenue'],
                    'completed_count' => $previousData['completed_count'],
                    'average_check' => $previousData['average_check'],
                ],
            ];
        } else {
            // Для general аналитики
            $currentData = $this->getGeneralData($businessId, $filters);
            $previousData = $this->getGeneralData($businessId, $previousFilters);

            return [
                'total_change' => $currentData['total'] - $previousData['total'],
                'total_change_percent' => $this->calculatePercentChange(
                    $previousData['total'],
                    $currentData['total']
                ),
                'completed_change' => $currentData['stats_by_status']['completed'] - $previousData['stats_by_status']['completed'],
                'completed_change_percent' => $this->calculatePercentChange(
                    $previousData['stats_by_status']['completed'],
                    $currentData['stats_by_status']['completed']
                ),
                'conversion_change' => $currentData['conversion_rate'] - $previousData['conversion_rate'],
                'cancellation_change' => $currentData['cancellation_rate'] - $previousData['cancellation_rate'],
                'previous_period' => [
                    'total' => $previousData['total'],
                    'completed' => $previousData['stats_by_status']['completed'],
                    'conversion_rate' => $previousData['conversion_rate'],
                    'cancellation_rate' => $previousData['cancellation_rate'],
                ],
            ];
        }
    }

    /**
     * Вычислить процент изменения
     */
    private function calculatePercentChange(float $previous, float $current): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Получить KPI данные для главной страницы аналитики
     */
    private function getKPIData(int $businessId): array
    {
        $cacheKey = 'analytics_kpi_'.$businessId;
        $cacheTags = ['analytics', "business_{$businessId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($businessId) {
            $now = Carbon::now();
            $last30Days = $now->copy()->subDays(30);
            $last90Days = $now->copy()->subDays(90);

            // Получаем все завершенные записи за последние 90 дней для расчета метрик
            $completedAppointments = \App\Models\Appointment::where('business_id', $businessId)
                ->where('status', 'completed')
                ->where('date', '>=', $last90Days->format('Y-m-d'))
                ->with(['client', 'service'])
                ->get();

            // Уникальные клиенты за последние 90 дней
            $uniqueClients = $completedAppointments->pluck('client_id')->unique();
            $totalClients = $uniqueClients->count();

            // Общая выручка за последние 90 дней
            $totalRevenue = $completedAppointments->sum(function ($appointment) {
                return $appointment->price ?? $appointment->service->price ?? 0;
            });

            // ARPU (Average Revenue Per User)
            $arpu = $totalClients > 0 ? round($totalRevenue / $totalClients, 2) : 0;

            // Retention Rate - клиенты с более чем одной записью за последние 90 дней
            $returningClients = $uniqueClients->filter(function ($clientId) use ($completedAppointments) {
                return $completedAppointments->where('client_id', $clientId)->count() > 1;
            })->count();

            $retentionRate = $totalClients > 0 ? round(($returningClients / $totalClients) * 100, 1) : 0;

            // Прогноз выручки на следующий месяц (средняя выручка за последние 3 месяца)
            // Оптимизировано: загружаем все данные одним запросом
            $threeMonthsAgo = $now->copy()->subMonths(3)->startOfMonth();
            $allMonthlyAppointments = \App\Models\Appointment::where('business_id', $businessId)
                ->where('status', 'completed')
                ->where('date', '>=', $threeMonthsAgo->format('Y-m-d'))
                ->where('date', '<', $now->copy()->startOfMonth()->format('Y-m-d'))
                ->with('service')
                ->get();

            // Группируем по месяцам
            $monthlyRevenues = [];
            $groupedByMonth = $allMonthlyAppointments->groupBy(function ($appointment) {
                return $appointment->date->format('Y-m');
            });

            for ($i = 0; $i < 3; $i++) {
                $monthKey = $now->copy()->subMonths($i + 1)->format('Y-m');
                $monthAppointments = $groupedByMonth->get($monthKey, collect());

                $monthRevenue = $monthAppointments->sum(function ($appointment) {
                    return $appointment->price ?? $appointment->service->price ?? 0;
                });

                if ($monthRevenue > 0) {
                    $monthlyRevenues[] = $monthRevenue;
                }
            }

            $averageMonthlyRevenue = count($monthlyRevenues) > 0 ? round(array_sum($monthlyRevenues) / count($monthlyRevenues), 0) : 0;
            $revenueForecast = $averageMonthlyRevenue;

            // Выручка за последние 30 дней (используем уже загруженные данные)
            $revenueLast30Days = $completedAppointments
                ->filter(function ($appointment) use ($last30Days) {
                    return $appointment->date->format('Y-m-d') >= $last30Days->format('Y-m-d');
                })
                ->sum(function ($appointment) {
                    return $appointment->price ?? $appointment->service->price ?? 0;
                });

            return [
                'retention_rate' => $retentionRate,
                'arpu' => $arpu,
                'revenue_forecast' => $revenueForecast,
                'revenue_last_30_days' => $revenueLast30Days,
                'total_clients' => $totalClients,
                'returning_clients' => $returningClients,
            ];
        });
    }

    /**
     * Получить данные аналитики клиентов
     */
    private function getClientsAnalyticsData(int $businessId, array $filters): array
    {
        $cacheKey = 'analytics_clients_'.$businessId.'_'.md5(json_encode($filters));
        $cacheTags = ['analytics', "business_{$businessId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($businessId, $filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            // Все клиенты с записями в периоде
            $appointmentsInPeriod = \App\Models\Appointment::where('business_id', $businessId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->with('client')
                ->get();

            $clientIdsInPeriod = $appointmentsInPeriod->pluck('client_id')->unique();

            // Новые клиенты (первая запись в периоде)
            // Оптимизация: получаем все первые записи одним запросом вместо N+1
            $firstAppointments = \App\Models\Appointment::where('business_id', $businessId)
                ->whereIn('client_id', $clientIdsInPeriod)
                ->select('client_id', \DB::raw('MIN(date) as first_date'))
                ->groupBy('client_id')
                ->get()
                ->keyBy('client_id');

            $newClients = [];
            $returningClients = [];

            foreach ($clientIdsInPeriod as $clientId) {
                $firstAppointment = $firstAppointments->get($clientId);

                if ($firstAppointment && $firstAppointment->first_date >= $startDate->format('Y-m-d')) {
                    $newClients[] = $clientId;
                } else {
                    $returningClients[] = $clientId;
                }
            }

            // LTV клиентов (только завершенные записи)
            $completedAppointments = \App\Models\Appointment::where('business_id', $businessId)
                ->where('status', 'completed')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->with(['client', 'service'])
                ->get();

            $clientsLTV = $completedAppointments->groupBy('client_id')->map(function ($group, $clientId) {
                $client = $group->first()->client;

                return [
                    'client_id' => $clientId,
                    'client_name' => $client ? $client->full_name : 'Неизвестный клиент',
                    'ltv' => $group->sum(function ($appointment) {
                        return $appointment->price ?? ($appointment->service ? ($appointment->service->price ?? 0) : 0);
                    }),
                    'appointments_count' => $group->count(),
                ];
            })->sortByDesc('ltv')->take(10)->values();

            $averageLTV = $clientsLTV->count() > 0 ? round($clientsLTV->avg('ltv'), 2) : 0;

            // Частота визитов
            $totalAppointments = $appointmentsInPeriod->count();
            $uniqueClientsCount = $clientIdsInPeriod->count();
            $visitFrequency = $uniqueClientsCount > 0 ? round($totalAppointments / $uniqueClientsCount, 2) : 0;

            // Привлечение новых клиентов по периодам
            $newClientsByPeriod = $this->getNewClientsByPeriod($businessId, $startDate, $endDate);

            return [
                'new_clients' => count($newClients),
                'returning_clients' => count($returningClients),
                'total_clients' => $uniqueClientsCount,
                'average_ltv' => $averageLTV,
                'top_clients' => $clientsLTV->toArray(),
                'visit_frequency' => $visitFrequency,
                'new_clients_by_period' => $newClientsByPeriod,
            ];
        });
    }

    /**
     * Получить привлечение новых клиентов по периодам
     * Optimized: loads all appointments with single query instead of N queries per day.
     */
    private function getNewClientsByPeriod(int $businessId, Carbon $startDate, Carbon $endDate): array
    {
        // Загружаем все записи за период одним запросом
        $allAppointments = \App\Models\Appointment::where('business_id', $businessId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // Определяем дату первой записи для каждого клиента
        $firstAppointmentByClient = [];
        foreach ($allAppointments as $appointment) {
            $clientId = $appointment->client_id;
            if (! isset($firstAppointmentByClient[$clientId])) {
                $firstAppointmentByClient[$clientId] = $appointment->date->format('Y-m-d');
            }
        }

        // Группируем записи по датам
        $appointmentsByDate = $allAppointments->groupBy(function ($appointment) {
            return $appointment->date->format('Y-m-d');
        });

        // Заполняем массив для всех дней
        $newClientsByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            // Клиенты, у которых первая запись в этот день
            $dayAppointments = $appointmentsByDate->get($dateStr, collect());
            $newClientsOnDay = $dayAppointments
                ->filter(function ($appointment) use ($firstAppointmentByClient, $dateStr) {
                    $clientId = $appointment->client_id;

                    return isset($firstAppointmentByClient[$clientId])
                        && $firstAppointmentByClient[$clientId] === $dateStr;
                })
                ->pluck('client_id')
                ->unique()
                ->count();

            $newClientsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'count' => $newClientsOnDay,
            ];

            $currentDate->addDay();
        }

        return $newClientsByDay;
    }

    /**
     * Получить аналитику по времени
     */
    private function getTimeAnalytics(int $businessId, array $filters): array
    {
        $cacheKey = 'analytics_time_'.$businessId.'_'.md5(json_encode($filters));
        $cacheTags = ['analytics', "business_{$businessId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($businessId, $filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            $query = \App\Models\Appointment::where('business_id', $businessId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($filters['service_id']) {
                $query->where('service_id', $filters['service_id']);
            }
            if ($filters['master_id']) {
                $query->where('master_id', $filters['master_id']);
            }
            if ($filters['location_id']) {
                $query->where('location_id', $filters['location_id']);
            }

            $appointments = $query->get();

            // Heatmap по часам и дням недели
            $heatmapData = [];
            $daysOfWeek = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];

            // Инициализация массива
            for ($day = 0; $day < 7; $day++) {
                for ($hour = 0; $hour < 24; $hour++) {
                    $heatmapData[$day][$hour] = 0;
                }
            }

            // Заполнение данных
            foreach ($appointments as $appointment) {
                if ($appointment->time) {
                    $time = Carbon::parse($appointment->time);
                    $hour = (int) $time->format('H');
                    $dayOfWeek = $appointment->date->dayOfWeek; // 0 = воскресенье, 1 = понедельник

                    // Конвертируем в формат где 0 = понедельник
                    $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                    if (isset($heatmapData[$dayIndex][$hour])) {
                        $heatmapData[$dayIndex][$hour]++;
                    }
                }
            }

            // Находим максимальное значение для нормализации
            $maxValue = 0;
            foreach ($heatmapData as $day) {
                foreach ($day as $count) {
                    if ($count > $maxValue) {
                        $maxValue = $count;
                    }
                }
            }

            // Статистика по дням недели
            $byDayOfWeek = [];
            foreach ($daysOfWeek as $index => $dayName) {
                $byDayOfWeek[] = [
                    'day' => $dayName,
                    'count' => array_sum($heatmapData[$index]),
                ];
            }

            // Статистика по часам
            $byHour = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $total = 0;
                for ($day = 0; $day < 7; $day++) {
                    $total += $heatmapData[$day][$hour] ?? 0;
                }
                $byHour[] = [
                    'hour' => $hour,
                    'count' => $total,
                ];
            }

            // Сезонность по месяцам
            $byMonth = [];
            foreach ($appointments as $appointment) {
                $monthKey = $appointment->date->format('Y-m');
                if (! isset($byMonth[$monthKey])) {
                    $date = Carbon::parse($appointment->date);
                    $byMonth[$monthKey] = [
                        'month' => $date->format('F Y'),
                        'label' => $date->locale('ru')->translatedFormat('M Y'),
                        'count' => 0,
                    ];
                }
                $byMonth[$monthKey]['count']++;
            }
            // Сортируем по ключу месяца
            ksort($byMonth);

            return [
                'heatmap' => $heatmapData,
                'max_value' => $maxValue,
                'by_day_of_week' => $byDayOfWeek,
                'by_hour' => $byHour,
                'by_month' => array_values($byMonth),
                'days_of_week' => $daysOfWeek,
            ];
        });
    }
}
