<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\NotificationSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSettingsController extends Controller
{
    /**
     * Показать страницу настроек уведомлений.
     */
    public function index()
    {
        $user = Auth::user();
        $settings = NotificationSettingsService::getUserSettings($user);
        $channels = NotificationSettingsService::getAvailableChannels();
        $typesByCategory = NotificationSettingsService::getNotificationTypesByCategory();

        return view('settings.notifications.index', compact('settings', 'channels', 'typesByCategory'));
    }

    /**
     * Обновить настройку для конкретного типа уведомления (AJAX).
     */
    public function update(Request $request)
    {
        $request->validate([
            'notification_type' => 'required|string',
            'channels' => 'required|array',
            'channels.*' => 'boolean',
        ]);

        $user = Auth::user();
        $notificationType = $request->input('notification_type');
        $channels = $request->input('channels');

        // Валидация: проверяем, что все каналы из запроса существуют в конфиге
        $availableChannels = array_keys(NotificationSettingsService::getAvailableChannels());
        foreach (array_keys($channels) as $channel) {
            if (!in_array($channel, $availableChannels)) {
                return response()->json([
                    'success' => false,
                    'message' => "Неизвестный канал: {$channel}",
                ], 422);
            }
        }

        NotificationSettingsService::updateSetting($user, $notificationType, $channels);

        return response()->json([
            'success' => true,
            'message' => 'Настройки успешно обновлены',
        ]);
    }

    /**
     * Получить все настройки пользователя (JSON API).
     */
    public function getSettings()
    {
        $user = Auth::user();
        $settings = NotificationSettingsService::getUserSettings($user);
        $channels = NotificationSettingsService::getAvailableChannels();

        return response()->json([
            'settings' => $settings,
            'channels' => $channels,
        ]);
    }
}
