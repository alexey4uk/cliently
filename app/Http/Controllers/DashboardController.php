<?php

namespace App\Http\Controllers;

use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
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
        $user = Auth::user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        // Проверяем наличие бизнеса
        if (! $business) {
            return redirect()->route('settings.business.create')
                ->with('info', 'Добро пожаловать! Сначала создайте свой бизнес.');
        }

        // Проверяем наличие локаций
        if ($business->locations->isEmpty()) {
            return redirect()->route('settings.locations.create')
                ->with('info', 'Добавьте локацию для записи клиентов.');
        }

        // Проверяем наличие услуг
        if ($business->services->isEmpty()) {
            return redirect()->route('services.create')
                ->with('info', 'Добавьте услуги, которые вы предлагаете.');
        }

        // Проверяем наличие мастеров
        if ($business->masters->isEmpty()) {
            return redirect()->route('settings.masters.create')
                ->with('info', 'Добавьте мастеров для предоставления услуг.');
        }

        // Получаем настройки виджетов
        $settings = $this->getSettings($user);
        $widgets = $this->getWidgetSettings($settings);
        $widgetOrder = $this->getWidgetOrder($settings);
        
        // Отладочный вывод для проверки порядка
        \Log::debug('Dashboard - widgetOrder', [
            'user_id' => $user->id,
            'widget_order_from_db' => $settings['dashboard']['widget_order'] ?? null,
            'widget_order_processed' => $widgetOrder,
        ]);

        // Кэширование данных (5 минут)
        $stats = Cache::remember('dashboard_stats_'.$user->id, 300, function () use ($business) {
            return $this->getStats($business->id);
        });

        $appointments = Cache::remember('dashboard_appointments_'.$user->id, 300, function () use ($business) {
            return $this->getAppointments($business->id);
        });

        $clients = Cache::remember('dashboard_clients_'.$user->id, 300, function () use ($business) {
            return $this->getRecentClients($business->id, 5);
        });

        // Данные для графиков (последние 7 дней)
        $chartData = Cache::remember('dashboard_charts_'.$user->id, 300, function () use ($business) {
            return $this->getChartData($business->id);
        });

        return view('dashboard', [
            'business' => $business,
            'stats' => $stats,
            'appointments' => $appointments,
            'clients' => $clients,
            'chartData' => $chartData,
            'widgets' => $widgets,
            'widgetOrder' => $widgetOrder,
            'lastUpdated' => now(),
        ]);
    }

    public function refresh()
    {
        $user = Auth::user();

        // Очистка кэша
        Cache::forget('dashboard_stats_'.$user->id);
        Cache::forget('dashboard_appointments_'.$user->id);
        Cache::forget('dashboard_clients_'.$user->id);
        Cache::forget('dashboard_charts_'.$user->id);

        return redirect()->back()->with('success', 'Данные обновлены');
    }

    /**
     * Получить общую статистику
     */
    private function getStats(int $businessId)
    {
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subWeek();
        $monthAgo = Carbon::now()->subMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $appointmentStats = $this->appointmentRepository->getDashboardStats($businessId);
        $newClientsCount = $this->clientRepository->getNewClientsCount($businessId, $monthAgo);

        // Дополнительные метрики
        $totalAppointments = \App\Models\Appointment::where('business_id', $businessId)->count();
        $totalClients = \App\Models\Client::where('business_id', $businessId)->count();
        
        // Рост клиентов
        $newClientsLastMonth = \App\Models\Client::where('business_id', $businessId)
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->count();
        $newClientsThisMonth = $newClientsCount;
        $clientsGrowthRate = $newClientsLastMonth > 0 
            ? round((($newClientsThisMonth - $newClientsLastMonth) / $newClientsLastMonth) * 100, 1)
            : ($newClientsThisMonth > 0 ? 100 : 0);

        // Рост записей
        $appointmentsLastMonth = \App\Models\Appointment::where('business_id', $businessId)
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->count();
        $appointmentsThisMonth = \App\Models\Appointment::where('business_id', $businessId)
            ->where('created_at', '>=', $monthAgo)
            ->count();
        $appointmentsGrowthRate = $appointmentsLastMonth > 0 
            ? round((($appointmentsThisMonth - $appointmentsLastMonth) / $appointmentsLastMonth) * 100, 1)
            : ($appointmentsThisMonth > 0 ? 100 : 0);

        // Статистика по статусам
        $pendingCount = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'pending')
            ->count();
        $confirmedCount = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'confirmed')
            ->count();
        $completedCount = \App\Models\Appointment::where('business_id', $businessId)
            ->where('status', 'completed')
            ->count();
        $cancelledCount = \App\Models\Appointment::where('business_id', $businessId)
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
        $appointmentsToday = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', $today->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();
        $completedToday = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', $today->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();

        // Завтра
        $tomorrow = Carbon::tomorrow();
        $appointmentsTomorrow = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', $tomorrow->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();

        // Статистика за периоды
        $appointmentsWeek = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', '>=', $weekAgo->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();
        $appointmentsMonth = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->count();
        
        $completedWeek = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', '>=', $weekAgo->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();
        $completedMonth = \App\Models\Appointment::where('business_id', $businessId)
            ->where('date', '>=', $monthAgo->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();

        // Новые клиенты за периоды
        $newClientsWeek = \App\Models\Client::where('business_id', $businessId)
            ->where('created_at', '>=', $weekAgo)
            ->count();

        // Активные клиенты (с записями за месяц)
        $activeClients = \App\Models\Client::where('business_id', $businessId)
            ->whereHas('appointments', function ($query) use ($monthAgo) {
                $query->where('date', '>=', $monthAgo->format('Y-m-d'));
            })
            ->distinct()
            ->count();

        // Процент активных клиентов
        $activeClientsRate = $totalClients > 0 
            ? round(($activeClients / $totalClients) * 100, 1) 
            : 0;

        return array_merge($appointmentStats, [
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
        ]);
    }

    /**
     * Получить данные для графиков
     */
    private function getChartData(int $businessId)
    {
        $days = 7;
        $appointmentsData = [];
        $clientsData = [];
        $completedData = [];
        $cancelledData = [];
        $labels = [];

        // Данные по дням недели
        $weekdayData = [];
        $weekdayLabels = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        for ($i = 0; $i < 7; $i++) {
            $weekdayData[] = 0;
        }

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d.m');

            // Записи за день
            $appointmentsData[] = \App\Models\Appointment::where('business_id', $businessId)
                ->where('date', $dateStr)
                ->where('status', '!=', 'cancelled')
                ->count();

            // Завершенные за день
            $completedData[] = \App\Models\Appointment::where('business_id', $businessId)
                ->where('date', $dateStr)
                ->where('status', 'completed')
                ->count();

            // Отмененные за день
            $cancelledData[] = \App\Models\Appointment::where('business_id', $businessId)
                ->where('date', $dateStr)
                ->where('status', 'cancelled')
                ->count();

            // Новые клиенты за день
            $clientsData[] = \App\Models\Client::where('business_id', $businessId)
                ->whereDate('created_at', $dateStr)
                ->count();

            // Статистика по дням недели (за последнюю неделю)
            $weekdayIndex = $date->dayOfWeek - 1; // 0 = Понедельник
            if ($weekdayIndex < 0) $weekdayIndex = 6; // Воскресенье = 6
            if ($i <= 7) { // Последние 7 дней для статистики по дням недели
                $weekdayData[$weekdayIndex] += \App\Models\Appointment::where('business_id', $businessId)
                    ->where('date', $dateStr)
                    ->where('status', '!=', 'cancelled')
                    ->count();
            }
        }

        return [
            'labels' => $labels,
            'appointments' => $appointmentsData,
            'clients' => $clientsData,
            'completed' => $completedData,
            'cancelled' => $cancelledData,
            'weekday_labels' => $weekdayLabels,
            'weekday_data' => $weekdayData,
        ];
    }

    /**
     * Получить записи для dashboard
     */
    private function getAppointments(int $businessId)
    {
        return $this->appointmentRepository->getDashboardAppointments($businessId);
    }

    /**
     * Получить недавних клиентов
     */
    private function getRecentClients(int $businessId, int $limit = 5)
    {
        return $this->clientRepository->getRecentForDashboard($businessId, $limit);
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
            // Настройки отдельных карточек метрик
            'stat_today' => true,
            'stat_week' => true,
            'stat_new_clients' => true,
            'stat_total_clients' => true,
            'stat_pending' => true,
            'stat_completed' => true,
            'stat_cancelled' => true,
            'stat_avg_per_day' => true,
            'quick_actions' => true,
            'appointments_chart' => true,
            'clients_chart' => true,
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
            'stats_header',
            'quick_actions',
            'appointments_chart',
            'clients_chart',
            'next_appointment',
            'today_appointments',
            'pending_appointments',
            'recent_clients',
            'weekly_chart',
        ];

        if (isset($settings['dashboard']['widget_order']) && !empty($settings['dashboard']['widget_order'])) {
            $existing = $settings['dashboard']['widget_order'];
            // Сохраняем порядок из существующих, добавляем новые виджеты в конец
            $result = [];
            // Сначала добавляем существующие виджеты в том порядке, в котором они сохранены
            foreach ($existing as $key) {
                if (in_array($key, $default) && !in_array($key, $result)) {
                    $result[] = $key;
                }
            }
            // Затем добавляем недостающие виджеты из дефолтного списка в конец
            foreach ($default as $key) {
                if (!in_array($key, $result)) {
                    $result[] = $key;
                }
            }
            return $result;
        }

        return $default;
    }
}
