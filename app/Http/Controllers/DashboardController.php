<?php

namespace App\Http\Controllers;

use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Traits\HasOwnDataFiltering;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    use HasOwnDataFiltering;

    private AppointmentRepositoryInterface $appointmentRepository;

    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        AppointmentRepositoryInterface $appointmentRepository,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->appointmentRepository = $appointmentRepository;
        $this->clientRepository = $clientRepository;
    }

    public function index()
    {
        $business = $this->getCurrentBusiness();

        // Проверяем наличие бизнеса
        if (! $business) {
            return redirect()->route('welcome')
                ->with('info', 'Добро пожаловать! Сначала создайте свой бизнес или примите приглашение.');
        }

        $business->load(['locations', 'services', 'masters']);

        // Получаем роль пользователя для проверки прав
        $role = $this->getCurrentBusinessRole();
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Проверяем наличие локаций (только если есть право на создание)
        if ($business->locations->isEmpty() && $role && $permissionService->hasPermission($role->id, 'client.locations.create')) {
            return redirect()->route('settings.locations.create')
                ->with('info', 'Добавьте локацию для записи клиентов.');
        }

        // Проверяем наличие услуг (только если есть право на создание)
        if ($business->services->isEmpty() && $role && $permissionService->hasPermission($role->id, 'client.services.create')) {
            return redirect()->route('services.create')
                ->with('info', 'Добавьте услуги, которые вы предлагаете.');
        }

        // Проверяем наличие мастеров (только если есть право на создание)
        if ($business->masters->isEmpty() && $role && $permissionService->hasPermission($role->id, 'client.masters.create')) {
            return redirect()->route('settings.masters.create')
                ->with('info', 'Добавьте мастеров для предоставления услуг.');
        }

        // Кэширование данных (5 минут)
        $user = Auth::user();
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Получаем данные с фильтрацией по правам
        $stats = Cache::remember('dashboard_stats_'.$user->id.'_'.($role ? $role->id : 'no_role'), 300, function () use ($business, $role) {
            return $this->getStats($business, $role);
        });

        $appointments = Cache::remember('dashboard_appointments_'.$user->id.'_'.($role ? $role->id : 'no_role'), 300, function () use ($business, $role) {
            return $this->getAppointments($business, $role);
        });

        $clients = Cache::remember('dashboard_clients_'.$user->id.'_'.($role ? $role->id : 'no_role'), 300, function () use ($business, $role) {
            return $this->getRecentClients($business, $role, 5);
        });

        // Финансовые метрики (если есть доступ)
        $financialStats = null;
        if ($role && $permissionService->hasPermission($role->id, 'client.analytics.view')) {
            $accessService = app(\App\Services\SubscriptionAccessService::class);
            if ($accessService->hasAccess($business, 'analytics_enabled', 'client.analytics.view')) {
                $financialStats = Cache::remember('dashboard_financial_'.$user->id.'_'.$role->id, 300, function () use ($business, $role) {
                    return $this->getFinancialStats($business, $role);
                });
            }
        }

        // Топ услуги (если есть доступ)
        $topServices = null;
        if (
            $role && $permissionService->hasPermission($role->id, 'client.analytics.view')
            && $permissionService->hasPermission($role->id, 'client.services.view')
        ) {
            $accessService = app(\App\Services\SubscriptionAccessService::class);
            if ($accessService->hasAccess($business, 'analytics_enabled', 'client.analytics.view')) {
                $topServices = Cache::remember('dashboard_top_services_'.$user->id.'_'.$role->id, 300, function () use ($business) {
                    return $this->getTopServices($business);
                });
            }
        }

        // Топ мастера (если есть доступ)
        $topMasters = null;
        if (
            $role && $permissionService->hasPermission($role->id, 'client.analytics.view')
            && $permissionService->hasPermission($role->id, 'client.masters.view')
        ) {
            $accessService = app(\App\Services\SubscriptionAccessService::class);
            if ($accessService->hasAccess($business, 'analytics_enabled', 'client.analytics.view')) {
                $topMasters = Cache::remember('dashboard_top_masters_'.$user->id.'_'.$role->id, 300, function () use ($business) {
                    return $this->getTopMasters($business);
                });
            }
        }

        // Статус подписки (если есть доступ)
        $subscriptionStatus = null;
        if ($role && $permissionService->hasPermission($role->id, 'client.subscription.view')) {
            $subscriptionStatus = Cache::remember('dashboard_subscription_'.$user->id.'_'.$role->id, 300, function () use ($business) {
                return $this->getSubscriptionStatus($business);
            });
        }

        return view('dashboard', [
            'business' => $business,
            'stats' => $stats,
            'appointments' => $appointments,
            'clients' => $clients,
            'financialStats' => $financialStats,
            'topServices' => $topServices,
            'topMasters' => $topMasters,
            'subscriptionStatus' => $subscriptionStatus,
        ]);
    }

    public function refresh()
    {
        $user = Auth::user();
        $business = $this->getCurrentBusiness();
        $role = $this->getCurrentBusinessRole();
        $roleId = $role ? $role->id : 'no_role';

        // Очистка кэша
        Cache::forget('dashboard_stats_'.$user->id.'_'.$roleId);
        Cache::forget('dashboard_appointments_'.$user->id.'_'.$roleId);
        Cache::forget('dashboard_clients_'.$user->id.'_'.$roleId);
        Cache::forget('dashboard_financial_'.$user->id.'_'.$roleId);
        Cache::forget('dashboard_top_services_'.$user->id.'_'.$roleId);
        Cache::forget('dashboard_top_masters_'.$user->id.'_'.$roleId);
        Cache::forget('dashboard_subscription_'.$user->id.'_'.$roleId);

        return redirect()->back()->with('success', 'Данные обновлены');
    }

    /**
     * Получить общую статистику с фильтрацией по правам
     */
    private function getStats(\App\Models\Business $business, ?\App\Models\BusinessRole $role)
    {
        $businessId = $business->id;
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subWeek();
        $monthAgo = Carbon::now()->subMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        // Фильтруем записи по правам
        $appointmentsQuery = \App\Models\Appointment::where('business_id', $businessId);
        if ($role) {
            $appointmentsQuery = $this->applyOwnDataFilterForAppointments(
                $appointmentsQuery,
                $business,
                $role->id,
                'client.appointments.view'
            );
        }

        // Фильтруем клиентов по правам
        $clientsQuery = \App\Models\Client::where('business_id', $businessId);
        if ($role) {
            $clientsQuery = $this->applyOwnDataFilterForClients(
                $clientsQuery,
                $business,
                $role->id,
                'client.clients.view'
            );
        }

        // Получаем статистику из отфильтрованных записей
        $totalAppointments = (clone $appointmentsQuery)->count();
        $totalClients = (clone $clientsQuery)->count();

        // Новые клиенты за месяц (с фильтрацией)
        $newClientsCount = (clone $clientsQuery)
            ->where('created_at', '>=', $monthAgo)
            ->count();

        // Рост клиентов
        $newClientsLastMonth = (clone $clientsQuery)
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->count();
        $newClientsThisMonth = $newClientsCount;
        $clientsGrowthRate = $newClientsLastMonth > 0
            ? round((($newClientsThisMonth - $newClientsLastMonth) / $newClientsLastMonth) * 100, 1)
            : ($newClientsThisMonth > 0 ? 100 : 0);

        // Рост записей
        $appointmentsLastMonth = (clone $appointmentsQuery)
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->count();
        $appointmentsThisMonth = (clone $appointmentsQuery)
            ->where('created_at', '>=', $monthAgo)
            ->count();
        $appointmentsGrowthRate = $appointmentsLastMonth > 0
            ? round((($appointmentsThisMonth - $appointmentsLastMonth) / $appointmentsLastMonth) * 100, 1)
            : ($appointmentsThisMonth > 0 ? 100 : 0);

        // Статистика по статусам
        $pendingCount = (clone $appointmentsQuery)
            ->where('status', 'pending')
            ->count();
        $confirmedCount = (clone $appointmentsQuery)
            ->where('status', 'confirmed')
            ->count();
        $completedCount = (clone $appointmentsQuery)
            ->where('status', 'completed')
            ->count();
        $cancelledCount = (clone $appointmentsQuery)
            ->where('status', 'cancelled')
            ->count();

        // Процентные показатели
        $completionRate = $totalAppointments > 0
            ? round(($completedCount / $totalAppointments) * 100, 1)
            : 0;
        $cancellationRate = $totalAppointments > 0
            ? round(($cancelledCount / $totalAppointments) * 100, 1)
            : 0;
        $confirmationRate = $totalAppointments > 0
            ? round(($confirmedCount / $totalAppointments) * 100, 1)
            : 0;

        // Средние значения
        $avgAppointmentsPerDay = $totalAppointments > 0
            ? round($totalAppointments / max(1, \Carbon\Carbon::parse(\App\Models\Appointment::where('business_id', $businessId)->min('created_at'))->diffInDays(now())), 1)
            : 0;
        $avgClientsPerAppointment = $totalAppointments > 0
            ? round($totalClients / $totalAppointments, 2)
            : 0;

        // Активность за сегодня
        $appointmentsToday = (clone $appointmentsQuery)
            ->where('date', $today->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();
        $completedToday = (clone $appointmentsQuery)
            ->where('date', $today->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();

        // Завтра
        $tomorrow = Carbon::tomorrow();
        $appointmentsTomorrow = (clone $appointmentsQuery)
            ->where('date', $tomorrow->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();

        // Статистика за периоды
        $appointmentsWeek = (clone $appointmentsQuery)
            ->where('date', '>=', $weekAgo->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();
        $appointmentsMonth = (clone $appointmentsQuery)
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();

        $completedWeek = (clone $appointmentsQuery)
            ->where('date', '>=', $weekAgo->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();
        $completedMonth = (clone $appointmentsQuery)
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();

        // Новые клиенты за периоды
        $newClientsWeek = (clone $clientsQuery)
            ->where('created_at', '>=', $weekAgo)
            ->count();

        // Активные клиенты (с записями за месяц)
        $activeClientsQuery = (clone $clientsQuery);
        $activeClients = $activeClientsQuery
            ->whereHas('appointments', function ($query) use ($monthAgo, $role, $business) {
                $query->where('date', '>=', $monthAgo->format('Y-m-d'));
                // Применяем фильтр по правам для записей внутри whereHas
                if ($role) {
                    $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                    if ($permissionService->hasOwnDataPermission($role->id, 'client.appointments.view')) {
                        $masterId = $this->getCurrentUserMasterId($business);
                        if ($masterId) {
                            $query->where('master_id', $masterId);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                    }
                }
            })
            ->distinct()
            ->count();

        // Процент активных клиентов
        $activeClientsRate = $totalClients > 0
            ? round(($activeClients / $totalClients) * 100, 1)
            : 0;

        return [
            'new_clients_month' => $newClientsCount,
            'new_clients_week' => $newClientsWeek,
            'total_appointments' => $totalAppointments,
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'active_clients_rate' => $activeClientsRate,
            'clients_growth_rate' => $clientsGrowthRate,
            'appointments_growth_rate' => $appointmentsGrowthRate,
            'pending_count' => $pendingCount,
            'confirmed_count' => $confirmedCount,
            'completed_count' => $completedCount,
            'cancelled_count' => $cancelledCount,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'confirmation_rate' => $confirmationRate,
            'avg_appointments_per_day' => $avgAppointmentsPerDay,
            'avg_clients_per_appointment' => $avgClientsPerAppointment,
            'appointments_today' => $appointmentsToday,
            'appointments_tomorrow' => $appointmentsTomorrow,
            'completed_today' => $completedToday,
            'appointments_week' => $appointmentsWeek,
            'appointments_month' => $appointmentsMonth,
            'completed_week' => $completedWeek,
            'completed_month' => $completedMonth,
        ];
    }

    /**
     * Получить записи для dashboard с фильтрацией по правам
     */
    private function getAppointments(\App\Models\Business $business, ?\App\Models\BusinessRole $role)
    {
        $query = \App\Models\Appointment::where('business_id', $business->id)
            ->where('date', Carbon::today()->format('Y-m-d'))
            ->with(['client', 'service', 'master'])
            ->orderBy('time');

        if ($role) {
            $query = $this->applyOwnDataFilterForAppointments(
                $query,
                $business,
                $role->id,
                'client.appointments.view'
            );
        }

        $upcoming = (clone $query)->where('status', 'confirmed')->get();
        $pending = (clone $query)->where('status', 'pending')->get();

        return [
            'upcoming' => $upcoming,
            'pending' => $pending,
        ];
    }

    /**
     * Получить недавних клиентов с фильтрацией по правам
     */
    private function getRecentClients(\App\Models\Business $business, ?\App\Models\BusinessRole $role, int $limit = 5)
    {
        $query = \App\Models\Client::where('business_id', $business->id)
            ->orderBy('created_at', 'desc');

        if ($role) {
            $query = $this->applyOwnDataFilterForClients(
                $query,
                $business,
                $role->id,
                'client.clients.view'
            );
        }

        return $query->limit($limit)->get();
    }

    /**
     * Получить финансовые метрики
     */
    private function getFinancialStats(\App\Models\Business $business, ?\App\Models\BusinessRole $role): array
    {
        $monthAgo = Carbon::now()->subMonth();
        $weekAgo = Carbon::now()->subWeek();
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        // Фильтруем записи по правам
        $appointmentsQuery = \App\Models\Appointment::where('business_id', $business->id)
            ->where('status', 'completed');
        if ($role) {
            $appointmentsQuery = $this->applyOwnDataFilterForAppointments(
                $appointmentsQuery,
                $business,
                $role->id,
                'client.appointments.view'
            );
        }

        // Выручка за месяц
        $revenueMonth = (clone $appointmentsQuery)
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->with('service')
            ->get()
            ->sum(function ($appointment) {
                return $appointment->price ?? $appointment->service->price ?? 0;
            });

        // Выручка за неделю
        $revenueWeek = (clone $appointmentsQuery)
            ->where('date', '>=', $weekAgo->format('Y-m-d'))
            ->with('service')
            ->get()
            ->sum(function ($appointment) {
                return $appointment->price ?? $appointment->service->price ?? 0;
            });

        // Выручка за прошлый месяц
        $revenueLastMonth = (clone $appointmentsQuery)
            ->whereBetween('date', [$twoMonthsAgo->format('Y-m-d'), $monthAgo->format('Y-m-d')])
            ->with('service')
            ->get()
            ->sum(function ($appointment) {
                return $appointment->price ?? $appointment->service->price ?? 0;
            });

        // Рост выручки
        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueMonth > 0 ? 100 : 0);

        // Средний чек
        $completedCount = (clone $appointmentsQuery)
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->count();
        $averageCheck = $completedCount > 0
            ? round($revenueMonth / $completedCount, 2)
            : 0;

        return [
            'revenue_month' => $revenueMonth,
            'revenue_week' => $revenueWeek,
            'revenue_growth' => $revenueGrowth,
            'average_check' => $averageCheck,
        ];
    }

    /**
     * Получить топ услуг
     */
    private function getTopServices(\App\Models\Business $business): array
    {
        $monthAgo = Carbon::now()->subMonth();

        $appointments = \App\Models\Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->with('service')
            ->get();

        $services = $appointments->groupBy('service_id')->map(function ($group, $serviceId) {
            $service = $group->first()->service;

            return [
                'service_id' => $serviceId,
                'service_name' => $service ? $service->name : 'Неизвестная услуга',
                'revenue' => $group->sum(function ($appointment) {
                    return $appointment->price ?? ($appointment->service ? $appointment->service->price : 0) ?? 0;
                }),
                'count' => $group->count(),
            ];
        })->values()->sortByDesc('revenue')->take(5)->values();

        return $services->toArray();
    }

    /**
     * Получить топ мастеров
     */
    private function getTopMasters(\App\Models\Business $business): array
    {
        $monthAgo = Carbon::now()->subMonth();

        $appointments = \App\Models\Appointment::where('business_id', $business->id)
            ->where('status', 'completed')
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->with(['master', 'service'])
            ->get();

        $masters = $appointments->groupBy('master_id')->map(function ($group, $masterId) {
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
        })->values()->sortByDesc('revenue')->take(5)->values();

        return $masters->toArray();
    }

    /**
     * Получить статус подписки
     */
    private function getSubscriptionStatus(\App\Models\Business $business): ?array
    {
        // Получаем владельца бизнеса
        $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
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

        $owner = \App\Models\User::find($ownerPivot->user_id);
        if (! $owner) {
            return null;
        }

        $subscription = $owner->activeSubscription();
        if (! $subscription) {
            return null;
        }

        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $usage = [];

        // Получаем использование для всех метрик
        $metrics = ['max_locations', 'max_masters', 'max_services', 'max_clients', 'max_appointments_per_month', 'max_business_users'];
        foreach ($metrics as $metric) {
            $currentUsage = $subscriptionService->getCurrentUsage($owner, $metric);
            $limit = $subscriptionService->getLimit($owner, $metric);
            $usage[$metric] = [
                'current' => $currentUsage,
                'limit' => $limit,
                'percentage' => $limit > 0 ? round(($currentUsage / $limit) * 100, 1) : 0,
                'warning' => $limit > 0 && ($currentUsage / $limit) > 0.8,
            ];
        }

        return [
            'plan_name' => $subscription->plan->name,
            'plan_slug' => $subscription->plan->slug,
            'plan_price' => $subscription->plan->price,
            'status' => $subscription->status,
            'ends_at' => $subscription->ends_at,
            'cancelled_at' => $subscription->cancelled_at,
            'is_cancelled' => $subscription->isCancelled(),
            'will_cancel_at_end' => $subscription->willCancelAtEnd(),
            'usage' => $usage,
        ];
    }
}
