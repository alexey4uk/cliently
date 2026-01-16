<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DashboardSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = $this->getSettings($user);

        return view('settings.dashboard', [
            'widgets' => $this->getWidgetSettings($settings),
            'widgetOrder' => $this->getWidgetOrder($settings),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'widgets' => 'required|array',
            'widget_order' => 'required|array',
        ]);

        $settings = $this->getSettings($user);
        $settings['dashboard'] = [
            'widgets' => $validated['widgets'],
            'widget_order' => $validated['widget_order'],
        ];

        $user->dashboard_settings = $settings;
        $user->save();

        // Очистка кэша
        Cache::forget('dashboard_stats_' . $user->id);
        Cache::forget('dashboard_appointments_' . $user->id);
        Cache::forget('dashboard_clients_' . $user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Получить настройки пользователя (декодирует JSON если нужно)
     */
    private function getSettings($user)
    {
        $settings = $user->dashboard_settings ?? [];

        // Если settings уже массив, вернём его
        if (is_array($settings)) {
            return $settings;
        }

        // Если settings JSON строка, декодируем
        if (is_string($settings)) {
            return json_decode($settings, true) ?? [];
        }

        return [];
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
