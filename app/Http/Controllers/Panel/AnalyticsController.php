<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics overview page.
     */
    public function index()
    {
        $this->authorize('panel.analytics.view');

        $data = $this->getOverviewData();

        return view('panel.analytics.index', compact('data'));
    }

    /**
     * Display the financial analytics page.
     */
    public function financial(Request $request)
    {
        $this->authorize('panel.analytics.financial');

        $filters = $this->getFilters($request);
        $data = $this->getFinancialData($filters);
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('panel.analytics.financial', compact('data', 'filters', 'plans'));
    }

    /**
     * Display the general analytics page.
     */
    public function general(Request $request)
    {
        $this->authorize('panel.analytics.general');

        $filters = $this->getFilters($request);
        $data = $this->getGeneralData($filters);

        return view('panel.analytics.general', compact('data', 'filters'));
    }

    /**
     * Display the subscriptions analytics page.
     */
    public function subscriptions(Request $request)
    {
        $this->authorize('panel.analytics.subscriptions');

        $filters = $this->getFilters($request);
        $data = $this->getSubscriptionsData($filters);
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('panel.analytics.subscriptions', compact('data', 'filters', 'plans'));
    }

    /**
     * Get overview data for the main analytics page.
     */
    private function getOverviewData(): array
    {
        $cacheKey = 'panel_analytics_overview_'.Auth::id();

        return Cache::remember($cacheKey, 300, function () {
            $today = Carbon::today();
            $monthAgo = Carbon::now()->subMonth();
            $twoMonthsAgo = Carbon::now()->subMonths(2);

            // Общие метрики
            $totalBusinesses = Business::count();
            $totalUsers = User::count();
            $totalClients = Client::count();
            $totalAppointments = Appointment::count();

            // Активные бизнесы (MAU)
            $activeBusinesses = Business::whereHas('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

            // Активные пользователи (MAU)
            $activeUsers = User::whereHas('businesses.appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

            // Выручка от подписок
            $revenueTotal = Invoice::where('status', 'paid')->sum('amount');
            $revenueMonth = Invoice::where('status', 'paid')
                ->where('paid_at', '>=', $monthAgo)
                ->sum('amount');

            // Активные подписки
            $activeSubscriptions = Subscription::where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>', now());
                })
                ->count();

            // Рост бизнесов
            $newBusinessesLastMonth = Business::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
            $newBusinessesThisMonth = Business::where('created_at', '>=', $monthAgo)->count();
            $businessGrowthRate = $newBusinessesLastMonth > 0
                ? round((($newBusinessesThisMonth - $newBusinessesLastMonth) / $newBusinessesLastMonth) * 100, 1)
                : ($newBusinessesThisMonth > 0 ? 100 : 0);

            // Рост пользователей
            $newUsersLastMonth = User::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
            $newUsersThisMonth = User::where('created_at', '>=', $monthAgo)->count();
            $userGrowthRate = $newUsersLastMonth > 0
                ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 1)
                : ($newUsersThisMonth > 0 ? 100 : 0);

            // Средние метрики на бизнес
            $avgAppointmentsPerBusiness = $totalBusinesses > 0
                ? round($totalAppointments / $totalBusinesses, 1)
                : 0;
            $avgClientsPerBusiness = $totalBusinesses > 0
                ? round($totalClients / $totalBusinesses, 1)
                : 0;

            // График роста (30 дней)
            $chartData = $this->getChartData(30);

            // Топ-5 бизнесов по записям
            $topBusinessesByAppointments = Business::withCount('appointments')
                ->orderBy('appointments_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'name' => $business->name,
                        'count' => $business->appointments_count,
                    ];
                });

            // Топ-5 бизнесов по клиентам
            $topBusinessesByClients = Business::withCount('clients')
                ->orderBy('clients_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'name' => $business->name,
                        'count' => $business->clients_count,
                    ];
                });

            // Последние регистрации
            $recentRegistrations = User::orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'name', 'email', 'created_at']);

            return [
                'total_businesses' => $totalBusinesses,
                'active_businesses' => $activeBusinesses,
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_clients' => $totalClients,
                'total_appointments' => $totalAppointments,
                'revenue_total' => $revenueTotal,
                'revenue_month' => $revenueMonth,
                'active_subscriptions' => $activeSubscriptions,
                'business_growth_rate' => $businessGrowthRate,
                'user_growth_rate' => $userGrowthRate,
                'avg_appointments_per_business' => $avgAppointmentsPerBusiness,
                'avg_clients_per_business' => $avgClientsPerBusiness,
                'chart_data' => $chartData,
                'top_businesses_by_appointments' => $topBusinessesByAppointments,
                'top_businesses_by_clients' => $topBusinessesByClients,
                'recent_registrations' => $recentRegistrations,
            ];
        });
    }

    /**
     * Get financial data.
     */
    private function getFinancialData(array $filters): array
    {
        $cacheKey = 'panel_analytics_financial_'.Auth::id().'_'.md5(json_encode($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            $query = Invoice::whereBetween('created_at', [$startDate, $endDate]);

            if ($filters['plan_id']) {
                $query->where('plan_id', $filters['plan_id']);
            }

            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            $invoices = $query->get();

            // Общая выручка
            $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
            $revenuePeriod = $invoices->where('status', 'paid')->sum('amount');

            // Выручка за периоды
            $revenueToday = Invoice::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('amount');
            $revenueWeek = Invoice::where('status', 'paid')
                ->where('paid_at', '>=', Carbon::now()->subWeek())
                ->sum('amount');
            $revenueMonth = Invoice::where('status', 'paid')
                ->where('paid_at', '>=', Carbon::now()->subMonth())
                ->sum('amount');

            // Средний чек
            $paidInvoices = Invoice::where('status', 'paid')->get();
            $averageCheck = $paidInvoices->count() > 0
                ? round($paidInvoices->sum('amount') / $paidInvoices->count(), 2)
                : 0;

            // Выручка по тарифам
            $revenueByPlan = Invoice::where('status', 'paid')
                ->select('plan_id', DB::raw('SUM(amount) as revenue'), DB::raw('COUNT(*) as count'))
                ->groupBy('plan_id')
                ->with('plan')
                ->get()
                ->map(function ($item) {
                    return [
                        'plan_id' => $item->plan_id,
                        'plan_name' => $item->plan ? $item->plan->name : 'Неизвестный тариф',
                        'revenue' => $item->revenue,
                        'count' => $item->count,
                    ];
                })
                ->sortByDesc('revenue')
                ->values();

            // Статистика по статусам
            $statusStats = [
                'paid' => Invoice::where('status', 'paid')->count(),
                'pending' => Invoice::where('status', 'pending')->count(),
                'failed' => Invoice::where('status', 'failed')->count(),
                'cancelled' => Invoice::where('status', 'cancelled')->count(),
                'refunded' => Invoice::where('status', 'refunded')->count(),
            ];

            // Динамика выручки
            $revenueByPeriod = $this->getRevenueByPeriod($startDate, $endDate, $filters);

            // Последние платежи
            $recentPayments = Invoice::where('status', 'paid')
                ->with(['user', 'plan'])
                ->orderBy('paid_at', 'desc')
                ->limit(10)
                ->get();

            return [
                'total_revenue' => $totalRevenue,
                'revenue_period' => $revenuePeriod,
                'revenue_today' => $revenueToday,
                'revenue_week' => $revenueWeek,
                'revenue_month' => $revenueMonth,
                'average_check' => $averageCheck,
                'revenue_by_plan' => $revenueByPlan,
                'status_stats' => $statusStats,
                'revenue_by_period' => $revenueByPeriod,
                'recent_payments' => $recentPayments,
            ];
        });
    }

    /**
     * Get general analytics data.
     */
    private function getGeneralData(array $filters): array
    {
        $cacheKey = 'panel_analytics_general_'.Auth::id().'_'.md5(json_encode($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            $query = Appointment::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            $appointments = $query->get();

            // Статистика по статусам
            $statsByStatus = [
                'pending' => Appointment::where('status', 'pending')->count(),
                'confirmed' => Appointment::where('status', 'confirmed')->count(),
                'completed' => Appointment::where('status', 'completed')->count(),
                'cancelled' => Appointment::where('status', 'cancelled')->count(),
            ];

            $total = $appointments->count();
            $completed = $statsByStatus['completed'];
            $cancelled = $statsByStatus['cancelled'];

            // Конверсия и процент отмен
            $conversionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            $cancellationRate = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0;

            // Средние метрики
            $totalBusinesses = Business::count();
            $avgAppointmentsPerBusiness = $totalBusinesses > 0
                ? round($total / $totalBusinesses, 1)
                : 0;
            $avgClientsPerBusiness = $totalBusinesses > 0
                ? round(Client::count() / $totalBusinesses, 1)
                : 0;

            // Динамика записей
            $appointmentsByPeriod = $this->getAppointmentsByPeriod($startDate, $endDate, $filters);

            // Статистика по бизнесам
            $statsByBusiness = Business::withCount(['appointments' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            }])
                ->orderBy('appointments_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'name' => $business->name,
                        'count' => $business->appointments_count,
                    ];
                });

            // Статистика по услугам
            $statsByService = Appointment::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select('service_id', DB::raw('COUNT(*) as count'))
                ->groupBy('service_id')
                ->with('service')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'service_id' => $item->service_id,
                        'service_name' => $item->service ? $item->service->name : 'Неизвестная услуга',
                        'count' => $item->count,
                    ];
                });

            // Статистика по мастерам
            $statsByMaster = Appointment::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select('master_id', DB::raw('COUNT(*) as count'))
                ->groupBy('master_id')
                ->with('master')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    $master = $item->master;
                    $masterName = $master ? trim($master->first_name.' '.($master->last_name ?? '')) : 'Неизвестный мастер';
                    return [
                        'master_id' => $item->master_id,
                        'master_name' => $masterName,
                        'count' => $item->count,
                    ];
                });

            return [
                'total' => $total,
                'stats_by_status' => $statsByStatus,
                'conversion_rate' => $conversionRate,
                'cancellation_rate' => $cancellationRate,
                'avg_appointments_per_business' => $avgAppointmentsPerBusiness,
                'avg_clients_per_business' => $avgClientsPerBusiness,
                'appointments_by_period' => $appointmentsByPeriod,
                'stats_by_business' => $statsByBusiness,
                'stats_by_service' => $statsByService,
                'stats_by_master' => $statsByMaster,
            ];
        });
    }

    /**
     * Get subscriptions analytics data.
     */
    private function getSubscriptionsData(array $filters): array
    {
        $cacheKey = 'panel_analytics_subscriptions_'.Auth::id().'_'.md5(json_encode($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            $query = Subscription::whereBetween('created_at', [$startDate, $endDate]);

            if ($filters['plan_id']) {
                $query->where('plan_id', $filters['plan_id']);
            }

            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            // Активные подписки
            $activeSubscriptions = Subscription::where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>', now());
                })
                ->count();

            // Пробные подписки
            $trialSubscriptions = Subscription::where('status', 'trial')
                ->where('trial_ends_at', '>', now())
                ->count();

            // Отмененные подписки
            $cancelledSubscriptions = Subscription::whereNotNull('cancelled_at')->count();

            // Распределение по тарифам
            $distributionByPlan = Subscription::select('plan_id', DB::raw('COUNT(*) as count'))
                ->groupBy('plan_id')
                ->with('plan')
                ->get()
                ->map(function ($item) {
                    return [
                        'plan_id' => $item->plan_id,
                        'plan_name' => $item->plan ? $item->plan->name : 'Неизвестный тариф',
                        'count' => $item->count,
                    ];
                })
                ->sortByDesc('count')
                ->values();

            // Статистика по статусам
            $statusStats = [
                'active' => Subscription::where('status', 'active')->count(),
                'trial' => Subscription::where('status', 'trial')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
                'expired' => Subscription::where('status', 'expired')->count(),
            ];

            // Конверсия пробных в платные
            $trialToPaid = Subscription::where('status', 'active')
                ->whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '<', now())
                ->count();
            $totalTrials = Subscription::where('status', 'trial')->count() + $trialToPaid;
            $conversionRate = $totalTrials > 0
                ? round(($trialToPaid / $totalTrials) * 100, 1)
                : 0;

            // Динамика подписок
            $subscriptionsByPeriod = $this->getSubscriptionsByPeriod($startDate, $endDate, $filters);

            // Новые подписки за период
            $newSubscriptions = Subscription::whereBetween('created_at', [$startDate, $endDate])
                ->with(['user', 'plan'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Отмененные подписки
            $cancelledSubscriptionsList = Subscription::whereNotNull('cancelled_at')
                ->whereBetween('cancelled_at', [$startDate, $endDate])
                ->with(['user', 'plan'])
                ->orderBy('cancelled_at', 'desc')
                ->limit(10)
                ->get();

            return [
                'active_subscriptions' => $activeSubscriptions,
                'trial_subscriptions' => $trialSubscriptions,
                'cancelled_subscriptions' => $cancelledSubscriptions,
                'distribution_by_plan' => $distributionByPlan,
                'status_stats' => $statusStats,
                'conversion_rate' => $conversionRate,
                'subscriptions_by_period' => $subscriptionsByPeriod,
                'new_subscriptions' => $newSubscriptions,
                'cancelled_subscriptions_list' => $cancelledSubscriptionsList,
            ];
        });
    }

    /**
     * Get chart data for overview.
     */
    private function getChartData(int $days = 30): array
    {
        $businessesData = [];
        $usersData = [];
        $appointmentsData = [];
        $clientsData = [];
        $revenueData = [];
        $labels = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d.m');

            $businessesData[] = Business::whereDate('created_at', $dateStr)->count();
            $usersData[] = User::whereDate('created_at', $dateStr)->count();
            $appointmentsData[] = Appointment::whereDate('created_at', $dateStr)->count();
            $clientsData[] = Client::whereDate('created_at', $dateStr)->count();
            $revenueData[] = Invoice::where('status', 'paid')
                ->whereDate('paid_at', $dateStr)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'businesses' => $businessesData,
            'users' => $usersData,
            'appointments' => $appointmentsData,
            'clients' => $clientsData,
            'revenue' => $revenueData,
        ];
    }

    /**
     * Get revenue by period.
     */
    private function getRevenueByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $revenueByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            $query = Invoice::where('status', 'paid')
                ->whereDate('paid_at', $dateStr);

            if ($filters['plan_id']) {
                $query->where('plan_id', $filters['plan_id']);
            }

            $dayRevenue = $query->sum('amount');

            $revenueByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'revenue' => $dayRevenue,
            ];

            $currentDate->addDay();
        }

        return $revenueByDay;
    }

    /**
     * Get appointments by period.
     */
    private function getAppointmentsByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $appointmentsByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            $query = Appointment::whereDate('date', $dateStr);

            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            $dayAppointments = $query->get();

            $appointmentsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'total' => $dayAppointments->count(),
                'completed' => $dayAppointments->where('status', 'completed')->count(),
                'cancelled' => $dayAppointments->where('status', 'cancelled')->count(),
            ];

            $currentDate->addDay();
        }

        return $appointmentsByDay;
    }

    /**
     * Get subscriptions by period.
     */
    private function getSubscriptionsByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $subscriptionsByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            $query = Subscription::whereDate('created_at', $dateStr);

            if ($filters['plan_id']) {
                $query->where('plan_id', $filters['plan_id']);
            }

            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            $daySubscriptions = $query->get();

            $subscriptionsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'total' => $daySubscriptions->count(),
                'active' => $daySubscriptions->where('status', 'active')->count(),
                'trial' => $daySubscriptions->where('status', 'trial')->count(),
            ];

            $currentDate->addDay();
        }

        return $subscriptionsByDay;
    }

    /**
     * Get filters from request.
     */
    private function getFilters(Request $request): array
    {
        return [
            'date_from' => $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d')),
            'date_to' => $request->get('date_to', Carbon::now()->format('Y-m-d')),
            'plan_id' => $request->get('plan_id'),
            'status' => $request->get('status'),
        ];
    }
}
