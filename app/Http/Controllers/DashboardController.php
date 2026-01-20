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
        Cache::forget('dashboard_stats_'.$user->id);
        Cache::forget('dashboard_appointments_'.$user->id);
        Cache::forget('dashboard_clients_'.$user->id);

        return redirect()->back()->with('success', 'Данные обновлены');
    }

    /**
     * Получить общую статистику
     */
    private function getStats(int $businessId)
    {
        $monthAgo = Carbon::now()->subMonth();

        $appointmentStats = $this->appointmentRepository->getDashboardStats($businessId);
        $newClientsCount = $this->clientRepository->getNewClientsCount($businessId, $monthAgo);

        return array_merge($appointmentStats, [
            'new_clients_month' => $newClientsCount,
        ]);
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
