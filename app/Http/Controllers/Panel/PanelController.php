<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    /**
     * Display the main panel page.
     * Универсальный дашборд, который показывает виджеты в зависимости от прав пользователя.
     */
    public function index()
    {
        $user = Auth::user();
        $cacheKey = 'panel_dashboard_'.$user->id;

        // Объединяем ВСЕ данные (включая графики) в один кэш (1 час)
        $dashboardData = Cache::remember($cacheKey, 3600, function () use ($user) {
            $data = $this->collectAllDashboardData($user);

            // Данные для графиков (если есть доступ к аналитике)
            $chartData = null;
            if ($user->can('panel.analytics.view')) {
                $chartData = $this->getChartData();
            }

            return [
                'stats' => $data['stats'],
                'chartData' => $chartData,
                'recentBusinesses' => $data['recentBusinesses'] ?? null,
                'recentUsers' => $data['recentUsers'] ?? null,
                'topBusinesses' => $data['topBusinesses'] ?? null,
                'recentAppointments' => $data['recentAppointments'] ?? null,
                'inactiveBusinesses' => $data['inactiveBusinesses'] ?? null,
                'activeBusinesses' => $data['activeBusinesses'] ?? null,
            ];
        });

        return view('panel.dashboard', $dashboardData);
    }

    /**
     * Дашборд для администратора
     * Полная статистика по всей системе
     */
    private function adminDashboard()
    {
        $cacheKey = 'panel_dashboard_admin_'.Auth::id();

        $stats = Cache::remember($cacheKey, 3600, function () {
            $today = Carbon::today();
            $weekAgo = Carbon::now()->subWeek();
            $monthAgo = Carbon::now()->subMonth();
            $twoMonthsAgo = Carbon::now()->subMonths(2);

            // Активные бизнесы (MAU - Monthly Active Businesses)
            $activeBusinessesMonth = Business::whereHas('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

            // ОПТИМИЗИРОВАНО: Активные пользователи (MAU) через pivot таблицу
            $activeUsersMonth = DB::table('users')
                ->join('business_user', 'users.id', '=', 'business_user.user_id')
                ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
                ->where('appointments.created_at', '>=', $monthAgo)
                ->distinct('users.id')
                ->count('users.id');

            // Активные бизнесы за неделю
            $activeBusinessesWeek = Business::whereHas('appointments', function ($query) use ($weekAgo) {
                $query->where('created_at', '>=', $weekAgo);
            })->distinct()->count();

            // Неактивные бизнесы (без активности за месяц)
            $inactiveBusinesses = Business::whereDoesntHave('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->count();

            // Средние метрики на бизнес
            $totalBusinesses = Business::count();
            $avgAppointmentsPerBusiness = $totalBusinesses > 0
                ? round(Appointment::count() / $totalBusinesses, 1)
                : 0;
            $avgClientsPerBusiness = $totalBusinesses > 0
                ? round(Client::count() / $totalBusinesses, 1)
                : 0;

            // Рост (месяц к месяцу)
            $newBusinessesLastMonth = Business::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
            $newBusinessesThisMonth = Business::where('created_at', '>=', $monthAgo)->count();
            $businessGrowthRate = $newBusinessesLastMonth > 0
                ? round((($newBusinessesThisMonth - $newBusinessesLastMonth) / $newBusinessesLastMonth) * 100, 1)
                : ($newBusinessesThisMonth > 0 ? 100 : 0);

            $newUsersLastMonth = User::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
            $newUsersThisMonth = User::where('created_at', '>=', $monthAgo)->count();
            $userGrowthRate = $newUsersLastMonth > 0
                ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 1)
                : ($newUsersThisMonth > 0 ? 100 : 0);

            return [
                // Основные метрики
                'total_businesses' => $totalBusinesses,
                'total_users' => User::count(),
                'total_clients' => Client::count(),
                'total_appointments' => Appointment::count(),

                // Активность
                'active_businesses_month' => $activeBusinessesMonth,
                'active_businesses_week' => $activeBusinessesWeek,
                'active_users_month' => $activeUsersMonth,
                'inactive_businesses' => $inactiveBusinesses,

                // Рост
                'new_businesses_week' => Business::where('created_at', '>=', $weekAgo)->count(),
                'new_businesses_month' => $newBusinessesThisMonth,
                'new_users_week' => User::where('created_at', '>=', $weekAgo)->count(),
                'new_users_month' => $newUsersThisMonth,
                'new_clients_week' => Client::where('created_at', '>=', $weekAgo)->count(),
                'new_clients_month' => Client::where('created_at', '>=', $monthAgo)->count(),

                // Метрики вовлеченности
                'business_growth_rate' => $businessGrowthRate,
                'user_growth_rate' => $userGrowthRate,
                'avg_appointments_per_business' => $avgAppointmentsPerBusiness,
                'avg_clients_per_business' => $avgClientsPerBusiness,

                // Активность за периоды
                'appointments_month' => Appointment::where('created_at', '>=', $monthAgo)->count(),
                'appointments_week' => Appointment::where('created_at', '>=', $weekAgo)->count(),
            ];
        });

        // Данные для графиков (последние 30 дней)
        $chartData = Cache::remember($cacheKey.'_charts', 3600, function () {
            $days = 30;

            $usersData = [];
            $businessesData = [];
            $clientsData = [];
            $appointmentsData = [];
            $activeBusinessesData = [];
            $labels = [];

            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('d.m');

                // Новые пользователи за день
                $usersData[] = User::whereDate('created_at', $dateStr)->count();

                // Новые бизнесы за день
                $businessesData[] = Business::whereDate('created_at', $dateStr)->count();

                // Новые клиенты за день
                $clientsData[] = Client::whereDate('created_at', $dateStr)->count();

                // Активность (записи) за день
                $appointmentsData[] = Appointment::whereDate('created_at', $dateStr)->count();

                // Активные бизнесы за день (бизнесы, которые создали записи в этот день)
                $activeBusinessesData[] = Business::whereHas('appointments', function ($query) use ($dateStr) {
                    $query->whereDate('created_at', $dateStr);
                })->distinct()->count();
            }

            return [
                'labels' => $labels,
                'users' => $usersData,
                'businesses' => $businessesData,
                'clients' => $clientsData,
                'appointments' => $appointmentsData,
                'active_businesses' => $activeBusinessesData,
            ];
        });

        // Последние бизнесы
        $recentBusinesses = Business::withCount(['appointments', 'clients', 'users'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Последние пользователи
        $recentUsers = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Статистика по бизнесам (топ 5)
        $topBusinesses = Business::withCount(['appointments', 'clients'])
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        return view('panel.dashboards.admin', [
            'stats' => $stats,
            'chartData' => $chartData,
            'recentBusinesses' => $recentBusinesses,
            'recentUsers' => $recentUsers,
            'topBusinesses' => $topBusinesses,
        ]);
    }

    /**
     * Дашборд для поддержки
     * Фокус на аналитике и поддержке пользователей
     */
    private function supportDashboard()
    {
        $cacheKey = 'panel_dashboard_support_'.Auth::id();
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subWeek();
        $monthAgo = Carbon::now()->subMonth();

        $stats = Cache::remember($cacheKey, 3600, function () use ($today, $weekAgo, $monthAgo) {
            return [
                // ОПТИМИЗИРОВАНО: Активность пользователей через pivot таблицу
                'active_users_week' => DB::table('users')
                    ->join('business_user', 'users.id', '=', 'business_user.user_id')
                    ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
                    ->where('appointments.created_at', '>=', $weekAgo)
                    ->distinct('users.id')
                    ->count('users.id'),
                'active_users_month' => DB::table('users')
                    ->join('business_user', 'users.id', '=', 'business_user.user_id')
                    ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
                    ->where('appointments.created_at', '>=', $monthAgo)
                    ->distinct('users.id')
                    ->count('users.id'),

                // Статистика записей
                'appointments_today' => Appointment::where('date', $today->format('Y-m-d'))
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'appointments_week' => Appointment::where('date', '>=', $weekAgo->format('Y-m-d'))
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'appointments_pending' => Appointment::where('status', 'pending')->count(),

                // Новые регистрации
                'new_users_week' => User::where('created_at', '>=', $weekAgo)->count(),
                'new_users_month' => User::where('created_at', '>=', $monthAgo)->count(),

                // Новые бизнесы
                'new_businesses_week' => Business::where('created_at', '>=', $weekAgo)->count(),
                'new_businesses_month' => Business::where('created_at', '>=', $monthAgo)->count(),

                // Проблемные записи (отмененные за неделю)
                'cancelled_week' => Appointment::where('status', 'cancelled')
                    ->where('updated_at', '>=', $weekAgo)
                    ->count(),
            ];
        });

        // Бизнесы без активности (за последний месяц)
        $inactiveBusinesses = Business::whereDoesntHave('appointments', function ($query) use ($monthAgo) {
            $query->where('created_at', '>=', $monthAgo);
        })
            ->withCount(['appointments', 'clients'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Последние записи
        $recentAppointments = Appointment::with(['business', 'client', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Бизнесы с наибольшей активностью
        $activeBusinesses = Business::withCount(['appointments' => function ($query) use ($weekAgo) {
            $query->where('created_at', '>=', $weekAgo);
        }])
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        return view('panel.dashboards.support', [
            'stats' => $stats,
            'inactiveBusinesses' => $inactiveBusinesses,
            'recentAppointments' => $recentAppointments,
            'activeBusinesses' => $activeBusinesses,
        ]);
    }

    /**
     * Общий дашборд для любой роли с доступом к панели
     * Базовая информация и быстрые действия
     */
    private function generalDashboard()
    {
        $cacheKey = 'panel_dashboard_general_'.Auth::id();

        $stats = Cache::remember($cacheKey, 3600, function () {
            $today = Carbon::today();
            $weekAgo = Carbon::now()->subWeek();

            return [
                'total_businesses' => Business::count(),
                'total_users' => User::count(),
                'total_clients' => Client::count(),
                'total_appointments' => Appointment::count(),
                'appointments_today' => Appointment::where('date', $today->format('Y-m-d'))
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'appointments_week' => Appointment::where('date', '>=', $weekAgo->format('Y-m-d'))
                    ->where('status', '!=', 'cancelled')
                    ->count(),
            ];
        });

        return view('panel.dashboards.general', [
            'stats' => $stats,
        ]);
    }

    /**
     * Собрать все возможные данные для dashboard
     */
    private function collectAllDashboardData($user)
    {
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subWeek();
        $monthAgo = Carbon::now()->subMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $data = [
            'stats' => [],
            'recentBusinesses' => null,
            'recentUsers' => null,
            'topBusinesses' => null,
            'recentAppointments' => null,
            'inactiveBusinesses' => null,
            'activeBusinesses' => null,
        ];

        // Базовые метрики (если есть доступ к просмотру бизнесов)
        if ($user->can('panel.businesses.view')) {
            $totalBusinesses = Business::count();
            $data['stats']['total_businesses'] = $totalBusinesses;

            // ОПТИМИЗИРОВАНО: Активные бизнесы (используем JOIN вместо whereHas)
            $data['stats']['active_businesses_month'] = DB::table('appointments')
                ->where('appointments.created_at', '>=', $monthAgo)
                ->distinct('business_id')
                ->count('business_id');

            $data['stats']['active_businesses_week'] = DB::table('appointments')
                ->where('appointments.created_at', '>=', $weekAgo)
                ->distinct('business_id')
                ->count('business_id');

            // ОПТИМИЗИРОВАНО: Неактивные бизнесы (используем LEFT JOIN)
            $data['stats']['inactive_businesses'] = DB::table('businesses')
                ->leftJoin('appointments', function ($join) use ($monthAgo) {
                    $join->on('businesses.id', '=', 'appointments.business_id')
                        ->where('appointments.created_at', '>=', $monthAgo);
                })
                ->whereNull('appointments.id')
                ->count('businesses.id');

            // Рост бизнесов
            $newBusinessesLastMonth = Business::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
            $newBusinessesThisMonth = Business::where('created_at', '>=', $monthAgo)->count();
            $data['stats']['business_growth_rate'] = $newBusinessesLastMonth > 0
                ? round((($newBusinessesThisMonth - $newBusinessesLastMonth) / $newBusinessesLastMonth) * 100, 1)
                : ($newBusinessesThisMonth > 0 ? 100 : 0);
            $data['stats']['new_businesses_week'] = Business::where('created_at', '>=', $weekAgo)->count();
            $data['stats']['new_businesses_month'] = $newBusinessesThisMonth;

            // Средние метрики
            $data['stats']['avg_appointments_per_business'] = $totalBusinesses > 0
                ? round(Appointment::count() / $totalBusinesses, 1)
                : 0;
            $data['stats']['avg_clients_per_business'] = $totalBusinesses > 0
                ? round(Client::count() / $totalBusinesses, 1)
                : 0;

            // Последние бизнесы
            $data['recentBusinesses'] = Business::withCount(['appointments', 'clients', 'users'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Топ бизнесы
            $data['topBusinesses'] = Business::withCount(['appointments', 'clients'])
                ->orderBy('appointments_count', 'desc')
                ->limit(5)
                ->get();

            // ОПТИМИЗИРОВАНО: Неактивные бизнесы (через подзапрос)
            $data['inactiveBusinesses'] = Business::select('businesses.*')
                ->selectRaw('(SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id) as appointments_count')
                ->selectRaw('(SELECT COUNT(*) FROM clients WHERE clients.business_id = businesses.id) as clients_count')
                ->whereNotIn('businesses.id', function ($query) use ($monthAgo) {
                    $query->select('business_id')
                        ->from('appointments')
                        ->where('created_at', '>=', $monthAgo)
                        ->distinct();
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // ОПТИМИЗИРОВАНО: Активные бизнесы (через подзапрос)
            $data['activeBusinesses'] = Business::select('businesses.*')
                ->selectRaw('(SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id AND appointments.created_at >= ?) as appointments_count', [$weekAgo])
                ->orderBy('appointments_count', 'desc')
                ->limit(5)
                ->get();
        }

        // Пользователи (если есть доступ)
        if ($user->can('panel.users.view')) {
            $data['stats']['total_users'] = User::count();

            // ОПТИМИЗИРОВАНО: Активные пользователи (через pivot таблицу business_user)
            $data['stats']['active_users_month'] = DB::table('users')
                ->join('business_user', 'users.id', '=', 'business_user.user_id')
                ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
                ->where('appointments.created_at', '>=', $monthAgo)
                ->distinct('users.id')
                ->count('users.id');

            $data['stats']['active_users_week'] = DB::table('users')
                ->join('business_user', 'users.id', '=', 'business_user.user_id')
                ->join('appointments', 'business_user.business_id', '=', 'appointments.business_id')
                ->where('appointments.created_at', '>=', $weekAgo)
                ->distinct('users.id')
                ->count('users.id');

            // Рост пользователей
            $newUsersLastMonth = User::whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
            $newUsersThisMonth = User::where('created_at', '>=', $monthAgo)->count();
            $data['stats']['user_growth_rate'] = $newUsersLastMonth > 0
                ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 1)
                : ($newUsersThisMonth > 0 ? 100 : 0);
            $data['stats']['new_users_week'] = User::where('created_at', '>=', $weekAgo)->count();
            $data['stats']['new_users_month'] = $newUsersThisMonth;

            // Последние пользователи
            $data['recentUsers'] = User::with('roles')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Клиенты (если есть доступ)
        if ($user->can('panel.clients.view')) {
            $data['stats']['total_clients'] = Client::count();
            $data['stats']['new_clients_week'] = Client::where('created_at', '>=', $weekAgo)->count();
            $data['stats']['new_clients_month'] = Client::where('created_at', '>=', $monthAgo)->count();
        }

        // Записи (если есть доступ)
        if ($user->can('panel.appointments.view')) {
            $data['stats']['total_appointments'] = Appointment::count();
            $data['stats']['appointments_today'] = Appointment::where('date', $today->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->count();
            $data['stats']['appointments_week'] = Appointment::where('date', '>=', $weekAgo->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->count();
            $data['stats']['appointments_month'] = Appointment::where('created_at', '>=', $monthAgo)->count();
            $data['stats']['appointments_pending'] = Appointment::where('status', 'pending')->count();
            $data['stats']['cancelled_week'] = Appointment::where('status', 'cancelled')
                ->where('updated_at', '>=', $weekAgo)
                ->count();

            // Последние записи
            $data['recentAppointments'] = Appointment::with(['business', 'client', 'service'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Данные для поддержки (активные и неактивные бизнесы)
        if ($user->can('panel.analytics.view') || $user->can('panel.support.view')) {
            // ОПТИМИЗИРОВАНО: Активные бизнесы за неделю
            if (! isset($data['activeBusinesses'])) {
                $data['activeBusinesses'] = Business::select('businesses.*')
                    ->selectRaw('(SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id AND appointments.created_at >= ?) as appointments_count', [$weekAgo])
                    ->orderBy('appointments_count', 'desc')
                    ->limit(5)
                    ->get();
            }

            // ОПТИМИЗИРОВАНО: Неактивные бизнесы
            if (! isset($data['inactiveBusinesses'])) {
                $data['inactiveBusinesses'] = Business::select('businesses.*')
                    ->selectRaw('(SELECT COUNT(*) FROM appointments WHERE appointments.business_id = businesses.id) as appointments_count')
                    ->selectRaw('(SELECT COUNT(*) FROM clients WHERE clients.business_id = businesses.id) as clients_count')
                    ->whereNotIn('businesses.id', function ($query) use ($monthAgo) {
                        $query->select('business_id')
                            ->from('appointments')
                            ->where('created_at', '>=', $monthAgo)
                            ->distinct();
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        return $data;
    }

    /**
     * Получить данные для графиков (ОПТИМИЗИРОВАНО)
     * Вместо 155+ запросов используем 5 запросов с GROUP BY
     */
    private function getChartData()
    {
        $days = 30;
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Один запрос для всех пользователей с группировкой по дате
        $usersGrouped = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Один запрос для всех бизнесов
        $businessesGrouped = Business::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Один запрос для всех клиентов
        $clientsGrouped = Client::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Один запрос для всех записей
        $appointmentsGrouped = Appointment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Один ОПТИМИЗИРОВАННЫЙ запрос для активных бизнесов
        // Вместо whereHas используем JOIN
        $activeBusinessesGrouped = DB::table('appointments')
            ->selectRaw('DATE(appointments.created_at) as date, COUNT(DISTINCT business_id) as count')
            ->whereBetween('appointments.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(appointments.created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        // Формируем массивы для каждого дня
        $usersData = [];
        $businessesData = [];
        $clientsData = [];
        $appointmentsData = [];
        $activeBusinessesData = [];
        $labels = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d.m');

            $usersData[] = $usersGrouped[$dateStr] ?? 0;
            $businessesData[] = $businessesGrouped[$dateStr] ?? 0;
            $clientsData[] = $clientsGrouped[$dateStr] ?? 0;
            $appointmentsData[] = $appointmentsGrouped[$dateStr] ?? 0;
            $activeBusinessesData[] = $activeBusinessesGrouped[$dateStr] ?? 0;
        }

        return [
            'labels' => $labels,
            'users' => $usersData,
            'businesses' => $businessesData,
            'clients' => $clientsData,
            'appointments' => $appointmentsData,
            'active_businesses' => $activeBusinessesData,
        ];
    }

    /**
     * Обновление данных дашборда
     */
    public function refresh()
    {
        $user = Auth::user();
        $cacheKey = 'panel_dashboard_'.$user->id;

        Cache::forget($cacheKey);

        return redirect()->back()->with('success', 'Данные обновлены');
    }
}
