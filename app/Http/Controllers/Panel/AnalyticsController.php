<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Master;
use App\Models\Plan;
use App\Models\Service;
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
        $plans = Plan::getActiveCached();

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
        $plans = Plan::getActiveCached();

        return view('panel.analytics.subscriptions', compact('data', 'filters', 'plans'));
    }

    /**
     * Get overview data for the main analytics page.
     */
    private function getOverviewData(): array
    {
        $userId = Auth::id();
        $cacheKey = 'panel_analytics_overview_' . $userId;
        $cacheTags = ['panel_analytics', "user_{$userId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        // ОПТИМИЗИРОВАНО: Увеличиваем время кеша для общей аналитики (3600 сек = 1 час)
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 3600, $callback);
            }

            return Cache::remember($key, 3600, $callback);
        };

        return $getCache($cacheKey, function () {
            $today = Carbon::today();
            $monthAgo = Carbon::now()->subMonth();
            $twoMonthsAgo = Carbon::now()->subMonths(2);

            // ОПТИМИЗИРОВАНО: Используем приблизительные значения из INFORMATION_SCHEMA для больших таблиц
            // Это в 100+ раз быстрее COUNT(*) на миллионах записей!
            $totalBusinesses = Business::count(); // Мало записей - быстро
            $totalUsers = User::count(); // Мало записей - быстро

            // Для больших таблиц используем приблизительное значение (обновляется при ANALYZE TABLE)
            try {
                $clientsInfo = DB::selectOne("SELECT table_rows FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'clients'");
                $totalClients = ($clientsInfo && isset($clientsInfo->table_rows)) ? (int)$clientsInfo->table_rows : Client::count();
            } catch (\Exception $e) {
                $totalClients = Client::count();
            }

            try {
                $appointmentsInfo = DB::selectOne("SELECT table_rows FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'appointments'");
                $totalAppointments = ($appointmentsInfo && isset($appointmentsInfo->table_rows)) ? (int)$appointmentsInfo->table_rows : Appointment::count();
            } catch (\Exception $e) {
                $totalAppointments = Appointment::count();
            }

            // ОПТИМИЗИРОВАНО: Активные бизнесы через JOIN
            $activeBusinesses = DB::table('businesses')
                ->join('appointments', 'businesses.id', '=', 'appointments.business_id')
                ->where('appointments.created_at', '>=', $monthAgo)
                ->distinct('businesses.id')
                ->count('businesses.id');

            // ОПТИМИЗИРОВАНО: Активные пользователи через JOIN
            $activeUsers = DB::table('users')
                ->join('business_user', 'users.id', '=', 'business_user.user_id')
                ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
                ->where('appointments.created_at', '>=', $monthAgo)
                ->distinct('users.id')
                ->count('users.id');

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

            // ОПТИМИЗИРОВАНО: Топ-5 бизнесов по записям через подзапрос
            $topBusinessesByAppointments = Business::query()
                ->selectRaw('businesses.*, 
                    (SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id) as appointments_count')
                ->orderByRaw('appointments_count DESC')
                ->limit(5)
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'name' => $business->name,
                        'count' => $business->appointments_count,
                    ];
                });

            // ОПТИМИЗИРОВАНО: Топ-5 бизнесов по клиентам через подзапрос
            $topBusinessesByClients = Business::query()
                ->selectRaw('businesses.*, 
                    (SELECT COUNT(*) FROM clients WHERE clients.business_id = businesses.id) as clients_count')
                ->orderByRaw('clients_count DESC')
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
        $userId = Auth::id();
        $cacheKey = 'panel_analytics_financial_' . $userId . '_' . md5(json_encode($filters));
        $cacheTags = ['panel_analytics', "user_{$userId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($filters) {
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

            // Кешируем общие метрики выручки (обновляются редко)
            $revenueMetrics = Cache::remember('analytics_revenue_metrics_' . today()->format('Y-m-d'), 600, function () {
                $now = Carbon::now();
                $today = $now->copy()->startOfDay();
                $weekAgo = $now->copy()->subWeek();
                $monthAgo = $now->copy()->subMonth();

                $totalRevenue = Invoice::where('status', 'paid')->sum('amount');

                $revenueToday = Invoice::where('status', 'paid')
                    ->whereBetween('paid_at', [$today, $now])
                    ->sum('amount');

                $revenueWeek = Invoice::where('status', 'paid')
                    ->where('paid_at', '>=', $weekAgo)
                    ->sum('amount');

                $revenueMonth = Invoice::where('status', 'paid')
                    ->where('paid_at', '>=', $monthAgo)
                    ->sum('amount');

                // Средний чек - используем один запрос вместо загрузки всех записей
                $avgCheckData = Invoice::where('status', 'paid')
                    ->selectRaw('SUM(amount) as total, COUNT(*) as count')
                    ->first();

                $averageCheck = $avgCheckData && $avgCheckData->count > 0
                    ? round($avgCheckData->total / $avgCheckData->count, 2)
                    : 0;

                return [
                    'total' => $totalRevenue,
                    'today' => $revenueToday,
                    'week' => $revenueWeek,
                    'month' => $revenueMonth,
                    'average' => $averageCheck,
                ];
            });

            $totalRevenue = $revenueMetrics['total'];
            $revenuePeriod = $invoices->where('status', 'paid')->sum('amount');
            $revenueToday = $revenueMetrics['today'];
            $revenueWeek = $revenueMetrics['week'];
            $revenueMonth = $revenueMetrics['month'];
            $averageCheck = $revenueMetrics['average'];

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

            // Статистика по статусам (используем уже загруженные данные для периода, общие - кешируем)
            $statusStatsPeriod = [
                'paid' => $invoices->where('status', 'paid')->count(),
                'pending' => $invoices->where('status', 'pending')->count(),
                'failed' => $invoices->where('status', 'failed')->count(),
                'cancelled' => $invoices->where('status', 'cancelled')->count(),
                'refunded' => $invoices->where('status', 'refunded')->count(),
            ];

            // Общая статистика по статусам (кешируем)
            $statusStats = Cache::remember('analytics_invoice_status_stats', 1800, function () {
                return [
                    'paid' => Invoice::where('status', 'paid')->count(),
                    'pending' => Invoice::where('status', 'pending')->count(),
                    'failed' => Invoice::where('status', 'failed')->count(),
                    'cancelled' => Invoice::where('status', 'cancelled')->count(),
                    'refunded' => Invoice::where('status', 'refunded')->count(),
                ];
            });

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
     * ОПТИМИЗИРОВАНО: Добавлено кеширование и оптимизированные запросы
     */
    private function getGeneralData(array $filters): array
    {
        $userId = Auth::id();
        $cacheKey = 'panel_analytics_general_' . $userId . '_' . md5(json_encode($filters));
        $cacheTags = ['panel_analytics', "user_{$userId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($filters) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();

            // КРИТИЧЕСКАЯ ОПТИМИЗАЦИЯ: НЕ загружаем все записи! Используем агрегацию
            $query = Appointment::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            // Используем только COUNT и группировку, НЕ get()!
            $appointmentsStats = $query
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Для совместимости создаем коллекцию с нужными данными
            $appointments = collect();

            // ОПТИМИЗИРОВАНО: Статистика по статусам из агрегации
            $statsByStatus = [
                'pending' => $appointmentsStats['pending'] ?? 0,
                'confirmed' => $appointmentsStats['confirmed'] ?? 0,
                'completed' => $appointmentsStats['completed'] ?? 0,
                'cancelled' => $appointmentsStats['cancelled'] ?? 0,
            ];

            $total = array_sum($appointmentsStats);
            $completed = $statsByStatus['completed'];
            $cancelled = $statsByStatus['cancelled'];

            // Конверсия и процент отмен
            $conversionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            $cancellationRate = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0;

            // Средние метрики
            $totalBusinesses = Business::count();

            // ОПТИМИЗИРОВАНО: Используем приблизительное значение для большой таблицы appointments
            try {
                $appointmentsInfo = DB::selectOne("SELECT table_rows FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'appointments'");
                $totalAppointmentsApprox = ($appointmentsInfo && isset($appointmentsInfo->table_rows)) ? (int)$appointmentsInfo->table_rows : $total;
            } catch (\Exception $e) {
                $totalAppointmentsApprox = $total;
            }

            $avgAppointmentsPerBusiness = $totalBusinesses > 0
                ? round($totalAppointmentsApprox / $totalBusinesses, 1)
                : 0;
            $avgClientsPerBusiness = $totalBusinesses > 0
                ? round(Client::count() / $totalBusinesses, 1)
                : 0;

            // Динамика записей
            $appointmentsByPeriod = $this->getAppointmentsByPeriod($startDate, $endDate, $filters);

            // ОПТИМИЗИРОВАНО: Статистика по бизнесам через JOIN вместо подзапроса
            $statsByBusiness = DB::table('businesses')
                ->leftJoin('appointments', function ($join) use ($startDate, $endDate) {
                    $join->on('businesses.id', '=', 'appointments.business_id')
                        ->whereBetween('appointments.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })
                ->select('businesses.id', 'businesses.name', DB::raw('COUNT(appointments.id) as appointments_count'))
                ->groupBy('businesses.id', 'businesses.name')
                ->orderBy('appointments_count', 'DESC')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'count' => (int)$item->appointments_count,
                    ];
                });

            // ОПТИМИЗИРОВАНО: Статистика по услугам без eager loading
            $serviceStats = Appointment::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select('service_id', DB::raw('COUNT(*) as count'))
                ->groupBy('service_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            $serviceIds = $serviceStats->pluck('service_id');
            $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

            $statsByService = $serviceStats->map(function ($item) use ($services) {
                return [
                    'service_id' => $item->service_id,
                    'service_name' => $services[$item->service_id]->name ?? 'Неизвестная услуга',
                    'count' => $item->count,
                ];
            });

            // ОПТИМИЗИРОВАНО: Статистика по мастерам без eager loading
            $masterStats = Appointment::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->select('master_id', DB::raw('COUNT(*) as count'))
                ->groupBy('master_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            $masterIds = $masterStats->pluck('master_id');
            $masters = Master::whereIn('id', $masterIds)->get()->keyBy('id');

            $statsByMaster = $masterStats->map(function ($item) use ($masters) {
                $master = $masters[$item->master_id] ?? null;
                $masterName = $master ? trim($master->first_name . ' ' . ($master->last_name ?? '')) : 'Неизвестный мастер';

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
        $userId = Auth::id();
        $cacheKey = 'panel_analytics_subscriptions_' . $userId . '_' . md5(json_encode($filters));
        $cacheTags = ['panel_analytics', "user_{$userId}"];

        // Проверяем поддержку тегов
        $supportsTags = method_exists(Cache::getStore(), 'tags');
        $getCache = function ($key, $callback) use ($cacheTags, $supportsTags) {
            if ($supportsTags) {
                return Cache::tags($cacheTags)->remember($key, 300, $callback);
            }

            return Cache::remember($key, 300, $callback);
        };

        return $getCache($cacheKey, function () use ($filters) {
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
     * ОПТИМИЗИРОВАНО: Используем индексы и оптимизированные запросы.
     */
    private function getChartData(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // ОПТИМИЗИРОВАНО: Используем DB::raw для DATE() чтобы использовать индекс created_at
        $businessesByDate = Business::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $usersByDate = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // ОПТИМИЗИРОВАНО: Для больших таблиц используем более эффективный запрос
        $appointmentsByDate = Appointment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $clientsByDate = Client::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $revenueByDate = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->pluck('total', 'date')
            ->toArray();

        // Заполняем массивы для всех дней
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

            $businessesData[] = $businessesByDate[$dateStr] ?? 0;
            $usersData[] = $usersByDate[$dateStr] ?? 0;
            $appointmentsData[] = $appointmentsByDate[$dateStr] ?? 0;
            $clientsData[] = $clientsByDate[$dateStr] ?? 0;
            $revenueData[] = $revenueByDate[$dateStr] ?? 0;
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
     * Optimized: loads all data with single query instead of N queries per day.
     */
    private function getRevenueByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate]);

        if ($filters['plan_id']) {
            $query->where('plan_id', $filters['plan_id']);
        }

        // Загружаем все данные одним запросом
        $revenueByDate = $query
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date')
            ->toArray();

        // Заполняем массив для всех дней
        $revenueByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            $revenueByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'revenue' => $revenueByDate[$dateStr] ?? 0,
            ];

            $currentDate->addDay();
        }

        return $revenueByDay;
    }

    /**
     * Get appointments by period.
     * Optimized: loads all data with single query instead of N queries per day.
     */
    private function getAppointmentsByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Appointment::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
        ]);

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        // КРИТИЧЕСКАЯ ОПТИМИЗАЦИЯ: Используем агрегацию вместо загрузки всех записей!
        $appointmentsByDate = $query
            ->selectRaw('date, 
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled')
            ->groupBy('date')
            ->get()
            ->keyBy(function ($item) {
                return $item->date;
            });

        // Заполняем массив для всех дней
        $appointmentsByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayStats = $appointmentsByDate->get($dateStr);

            $appointmentsByDay[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('d.m'),
                'total' => $dayStats ? (int)$dayStats->total : 0,
                'completed' => $dayStats ? (int)$dayStats->completed : 0,
                'cancelled' => $dayStats ? (int)$dayStats->cancelled : 0,
            ];

            $currentDate->addDay();
        }

        return $appointmentsByDay;
    }

    /**
     * Get subscriptions by period.
     * Optimized: loads all data with single query instead of N queries per day.
     */
    private function getSubscriptionsByPeriod(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Subscription::whereBetween('created_at', [$startDate, $endDate]);

        if ($filters['plan_id']) {
            $query->where('plan_id', $filters['plan_id']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Загружаем все данные одним запросом
        $subscriptions = $query->get();

        // Группируем по датам
        $groupedByDate = $subscriptions->groupBy(function ($subscription) {
            return $subscription->created_at->format('Y-m-d');
        });

        // Заполняем массив для всех дней
        $subscriptionsByDay = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $daySubscriptions = $groupedByDate->get($dateStr, collect());

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
