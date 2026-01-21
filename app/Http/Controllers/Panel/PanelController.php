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
     * Определяет роль пользователя и показывает соответствующий дашборд.
     */
    public function index()
    {
        $user = Auth::user();

        // Проверяем роль и показываем соответствующий дашборд
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('support')) {
            return $this->supportDashboard();
        } else {
            // Для любой другой роли с доступом к панели
            return $this->generalDashboard();
        }
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
     * Обновление данных дашборда
     */
    public function refresh()
    {
        $user = Auth::user();
        $cacheKey = 'panel_dashboard_';
        
        if ($user->hasRole('admin')) {
            $cacheKey .= 'admin_';
        } elseif ($user->hasRole('support')) {
            $cacheKey .= 'support_';
        } else {
            $cacheKey .= 'general_';
        }
        
        $cacheKey .= $user->id;
        
        Cache::forget($cacheKey);
        
        return redirect()->back()->with('success', 'Данные обновлены');
    }
}
