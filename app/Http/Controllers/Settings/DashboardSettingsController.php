<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = $this->getSettings($user);
        $widgetOrder = $this->getWidgetOrder($settings);
        
        // Отладочный вывод для проверки порядка
        \Log::debug('Settings - widgetOrder', [
            'user_id' => $user->id,
            'widget_order_from_db' => $settings['dashboard']['widget_order'] ?? null,
            'widget_order_processed' => $widgetOrder,
        ]);

        return view('settings.dashboard', [
            'widgets' => $this->getWidgetSettings($settings),
            'widgetOrder' => $widgetOrder,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'widgets' => 'required|array',
            'widget_order' => 'nullable|array',
        ]);

        $settings = $this->getSettings($user);
        
        // Используем переданный порядок виджетов, если он есть
        // Если порядок передан, используем его (даже если это пустой массив)
        if (isset($validated['widget_order'])) {
            $widgetOrder = $validated['widget_order'];
        } else {
            // Если порядок не передан, сохраняем текущий
            $widgetOrder = $settings['dashboard']['widget_order'] ?? $this->getWidgetOrder([]);
        }
        
        $settings['dashboard'] = [
            'widgets' => $validated['widgets'],
            'widget_order' => $widgetOrder,
        ];

        $user->dashboard_settings = $settings;
        $user->save();
        
        // Отладочный вывод для проверки сохранения
        \Log::debug('Settings - Saved widgetOrder', [
            'user_id' => $user->id,
            'widget_order_saved' => $widgetOrder,
            'widget_order_from_request' => $validated['widget_order'] ?? null,
        ]);

        // Очистка кэша
        Cache::forget('dashboard_stats_'.$user->id);
        Cache::forget('dashboard_appointments_'.$user->id);
        Cache::forget('dashboard_clients_'.$user->id);
        Cache::forget('dashboard_charts_'.$user->id);

        return response()->json(['success' => true]);
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
