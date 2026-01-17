<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('businesses');
        $business = $user->businesses->first();

        if (! $business) {
            return redirect()->route('onboarding.business');
        }

        // Получаем настройки виджетов
        $settings = $this->getSettings($user);
        $widgets = $this->getWidgetSettings($settings);
        $widgetOrder = $this->getWidgetOrder($settings);

        // Кэширование данных (5 минут)
        $stats = Cache::remember('dashboard_stats_' . $user->id, 300, function () use ($business) {
            return $this->getStats($business);
        });

        $appointments = Cache::remember('dashboard_appointments_' . $user->id, 300, function () use ($business) {
            return $this->getAppointments($business);
        });

        $clients = Cache::remember('dashboard_clients_' . $user->id, 300, function () use ($business) {
            return $this->getRecentClients($business, 5);
        });

        return view('dashboard', [
            'business' => $business,
            'stats' => $stats,
            'appointments' => $appointments,
            'clients' => $clients,
            'widgets' => $widgets,
            'widgetOrder' => $widgetOrder,
            'lastUpdated' => now(),
        ]);
    }

    public function refresh()
    {
        $user = Auth::user();

        // Очистка кэша
        Cache::forget('dashboard_stats_' . $user->id);
        Cache::forget('dashboard_appointments_' . $user->id);
        Cache::forget('dashboard_clients_' . $user->id);

        return redirect()->back()->with('success', 'Данные обновлены');
    }

    /**
     * Получить общую статистику
     */
    private function getStats($business)
    {
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subWeek();
        $monthAgo = Carbon::now()->subMonth();

        return [
            'today' => $business->appointments()
                ->where('date', $today->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->count(),
            'completed_week' => $business->appointments()
                ->where('date', '>=', $weekAgo->format('Y-m-d'))
                ->where('status', 'completed')
                ->count(),
            'new_clients_month' => $business->clients()
                ->where('created_at', '>=', $monthAgo)
                ->count(),
        ];
    }

    /**
     * Получить записи для dashboard
     */
    private function getAppointments($business)
    {
        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');

        // Записи на сегодня
        $todayAppointments = $business->appointments()
            ->where('date', $today->format('Y-m-d'))
            ->whereIn('status', ['confirmed', 'completed'])
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('time', 'asc')
            ->get();

        // Записи, требующие внимания
        $pendingAppointments = $business->appointments()
            ->where('status', 'pending')
            ->where('date', '>=', $today->format('Y-m-d'))
            ->with(['client', 'service', 'master', 'location'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->limit(5)
            ->get();

        // Следующая запись
        $nextAppointment = $todayAppointments
            ->filter(function ($appointment) use ($currentTime) {
                return $appointment->time >= $currentTime && $appointment->status === 'confirmed';
            })
            ->first();

        // Разделяем записи на выполненные и предстоящие
        $completedAppointments = $todayAppointments->where('status', 'completed');
        $upcomingAppointments = $todayAppointments->where('status', 'confirmed');

        // Исключаем следующую запись из основного списка
        $upcomingAppointmentsWithoutNext = $upcomingAppointments->filter(function ($appointment) use ($nextAppointment) {
            return ! $nextAppointment || $appointment->id !== $nextAppointment->id;
        });

        return [
            'today' => $todayAppointments,
            'completed' => $completedAppointments,
            'upcoming' => $upcomingAppointmentsWithoutNext,
            'pending' => $pendingAppointments,
            'next' => $nextAppointment,
            'todayDate' => $today->locale('ru')->isoFormat('D MMMM'),
        ];
    }

    /**
     * Получить недавних клиентов
     */
    private function getRecentClients($business, $limit = 5)
    {
        return $business->clients()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Получить настройки пользователя
     */
    private function getSettings($user)
    {
        return $user->dashboard_settings ?? [];
    }

    /**
     * Получить настройки виджетов
     */
    private function getWidgetSettings($settings)
    {
        $default = [
            'stats_header' => true,
            'quick_actions' => true,
            'next_appointment' => true,
            'today_appointments' => true,
            'pending_appointments' => true,
            'recent_clients' => true,
            'weekly_chart' => false,
        ];

        if (isset($settings['dashboard']['widgets'])) {
            return array_merge($default, $settings['dashboard']['widgets']);
        }

        return $default;
    }

    /**
     * Получить порядок виджетов
     */
    private function getWidgetOrder($settings)
    {
        $default = [
            'next_appointment',
            'today_appointments',
            'pending_appointments',
            'recent_clients',
            'weekly_chart',
        ];

        if (isset($settings['dashboard']['widget_order'])) {
            return $settings['dashboard']['widget_order'];
        }

        return $default;
    }
}
