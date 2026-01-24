<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use App\Services\SubscriptionAccessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AnalyticsController extends Controller
{
    public function index()
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
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

        return view('analytics.index', [
            'business' => $business,
        ]);
    }

    public function financial(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
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

        return view('analytics.financial', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
        ]);
    }

    public function general(Request $request)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
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

        return view('analytics.general', [
            'business' => $business,
            'data' => $data,
            'filters' => $filters,
            'hasAdvancedAnalytics' => $hasAdvancedAnalytics,
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

        return Cache::remember($cacheKey, 300, function () use ($businessId, $filters) {
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
                return ($appointment->price ?? $appointment->service->price ?? 0);
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

        return Cache::remember($cacheKey, 300, function () use ($businessId, $filters) {
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

            $revenueByDay = [];
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayRevenue = $appointments
                    ->filter(function ($appointment) use ($dateStr) {
                        return $appointment->date->format('Y-m-d') === $dateStr;
                    })
                    ->sum(function ($appointment) {
                        return ($appointment->price ?? $appointment->service->price ?? 0);
                    });

            $revenueByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'revenue' => $dayRevenue,
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
                    return ($appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0);
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
                    return ($appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0);
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
                    return ($appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0);
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
        
        if (!$owner) {
            return false;
        }
        
        $limit = $subscriptionService->getLimit($owner, 'advanced_analytics_enabled');
        return $limit === true;
    }

    /**
     * Получить владельца бизнеса
     */
    private function getBusinessOwner(\App\Models\Business $business): ?\App\Models\User
    {
        $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
        
        if (!$ownerRole) {
            return null;
        }

        $ownerPivot = \Illuminate\Support\Facades\DB::table('business_user')
            ->where('business_id', $business->id)
            ->where('role_id', $ownerRole->id)
            ->first();
        
        if (!$ownerPivot) {
            return null;
        }

        return \App\Models\User::find($ownerPivot->user_id);
    }

}
