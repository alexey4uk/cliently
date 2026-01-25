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
        $allCategories = NotificationSettingsService::getNotificationTypesByCategory();
        // В клиентской части исключаем админские уведомления
        $typesByCategory = array_filter($allCategories, function ($key) {
            return $key !== 'admin';
        }, ARRAY_FILTER_USE_KEY);

        // Получаем бота для генерации ссылки привязки
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();
        $botUsername = $bot ? $bot->name : null;

        // Генерируем токен, если отсутствует
        if (empty($user->telegram_token)) {
            $user->telegram_token = \Illuminate\Support\Str::random(32);
            $user->save();
        }

        $telegramLink = $botUsername && $user->telegram_token
            ? "https://t.me/{$botUsername}?start=user_auth_{$user->telegram_token}"
            : null;

        return view('settings.notifications.index', compact('settings', 'channels', 'typesByCategory', 'user', 'telegramLink'));
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
            'enabled' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $notificationType = $request->input('notification_type');
        $channels = $request->input('channels');
        $enabled = $request->has('enabled') ? (bool) $request->input('enabled') : null;

        $availableChannels = array_keys(NotificationSettingsService::getAvailableChannels());
        foreach (array_keys($channels) as $channel) {
            if (! in_array($channel, $availableChannels)) {
                return response()->json([
                    'success' => false,
                    'message' => "Неизвестный канал: {$channel}",
                ], 422);
            }
        }

        NotificationSettingsService::updateSetting($user, $notificationType, $channels, $enabled);

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

    /**
     * Отвязать Telegram аккаунт пользователя.
     */
    public function disconnectTelegram()
    {
        $user = Auth::user();
        
        // Уведомляем об отключении Telegram перед отвязкой
        if ($user->telegram_chat_id) {
            \App\Services\TelegramNotificationService::notifyDisconnected($user);
        }
        
        $user->telegram_chat_id = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Telegram аккаунт успешно отвязан',
        ]);
    }
}
