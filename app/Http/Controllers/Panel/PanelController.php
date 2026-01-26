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

        // Объединяем ВСЕ данные (включая графики) в один кэш
        $dashboardData = Cache::remember($cacheKey, 300, function () use ($user) {
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

        $stats = Cache::remember($cacheKey, 300, function () {
            $today = Carbon::today();
            $weekAgo = Carbon::now()->subWeek();
            $monthAgo = Carbon::now()->subMonth();
            $twoMonthsAgo = Carbon::now()->subMonths(2);

            // Активные бизнесы (MAU - Monthly Active Businesses)
            $activeBusinessesMonth = Business::whereHas('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

            // Активные пользователи (MAU)
            $activeUsersMonth = User::whereHas('businesses.appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

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
        $chartData = Cache::remember($cacheKey.'_charts', 300, function () {
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

        $stats = Cache::remember($cacheKey, 300, function () use ($today, $weekAgo, $monthAgo) {
            return [
                // Активность пользователей
                'active_users_week' => User::whereHas('businesses.appointments', function ($query) use ($weekAgo) {
                    $query->where('created_at', '>=', $weekAgo);
                })->distinct()->count(),
                'active_users_month' => User::whereHas('businesses.appointments', function ($query) use ($monthAgo) {
                    $query->where('created_at', '>=', $monthAgo);
                })->distinct()->count(),

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

        $stats = Cache::remember($cacheKey, 300, function () {
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

            // Активные бизнесы
            $data['stats']['active_businesses_month'] = Business::whereHas('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

            $data['stats']['active_businesses_week'] = Business::whereHas('appointments', function ($query) use ($weekAgo) {
                $query->where('created_at', '>=', $weekAgo);
            })->distinct()->count();

            // Неактивные бизнесы
            $data['stats']['inactive_businesses'] = Business::whereDoesntHave('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->count();

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

            // Неактивные бизнесы
            $data['inactiveBusinesses'] = Business::whereDoesntHave('appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })
                ->withCount(['appointments', 'clients'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Активные бизнесы
            $data['activeBusinesses'] = Business::withCount(['appointments' => function ($query) use ($weekAgo) {
                $query->where('created_at', '>=', $weekAgo);
            }])
                ->orderBy('appointments_count', 'desc')
                ->limit(5)
                ->get();
        }

        // Пользователи (если есть доступ)
        if ($user->can('panel.users.view')) {
            $data['stats']['total_users'] = User::count();

            // Активные пользователи
            $data['stats']['active_users_month'] = User::whereHas('businesses.appointments', function ($query) use ($monthAgo) {
                $query->where('created_at', '>=', $monthAgo);
            })->distinct()->count();

            // Активные пользователи за неделю (для поддержки)
            $data['stats']['active_users_week'] = User::whereHas('businesses.appointments', function ($query) use ($weekAgo) {
                $query->where('created_at', '>=', $weekAgo);
            })->distinct()->count();

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
            // Активные бизнесы за неделю
            if (! isset($data['activeBusinesses'])) {
                $data['activeBusinesses'] = Business::withCount(['appointments' => function ($query) use ($weekAgo) {
                    $query->where('created_at', '>=', $weekAgo);
                }])
                    ->orderBy('appointments_count', 'desc')
                    ->limit(5)
                    ->get();
            }

            // Неактивные бизнесы
            if (! isset($data['inactiveBusinesses'])) {
                $data['inactiveBusinesses'] = Business::whereDoesntHave('appointments', function ($query) use ($monthAgo) {
                    $query->where('created_at', '>=', $monthAgo);
                })
                    ->withCount(['appointments', 'clients'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        return $data;
    }

    /**
     * Получить данные для графиков
     */
    private function getChartData()
    {
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

            $usersData[] = User::whereDate('created_at', $dateStr)->count();
            $businessesData[] = Business::whereDate('created_at', $dateStr)->count();
            $clientsData[] = Client::whereDate('created_at', $dateStr)->count();
            $appointmentsData[] = Appointment::whereDate('created_at', $dateStr)->count();
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
