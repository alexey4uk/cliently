<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\NotificationSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationSettingsController extends Controller
{
    /**
     * Показать страницу настроек уведомлений (админка).
     * Без client.access — только админские подкатегории. С client.access — все категории.
     */
    public function index()
    {
        $user = Auth::user();
        $settings = NotificationSettingsService::getUserSettings($user);
        $channels = NotificationSettingsService::getAvailableChannels();

        $hasClientAccess = $user->can('client.access');

        if (! $hasClientAccess) {
            $subcategories = NotificationSettingsService::getAdminTypesBySubcategory($user);
            $typesByCategory = [];
            $categoryNames = [];
            $categoryIcons = [];
            foreach ($subcategories as $key => $data) {
                $typesByCategory[$key] = $data['types'];
                $categoryNames[$key] = $data['label'];
                $categoryIcons[$key] = $data['icon'];
            }
        } else {
            $all = NotificationSettingsService::getNotificationTypesByCategory();
            $adminSub = NotificationSettingsService::getAdminTypesBySubcategory($user);
            $typesByCategory = [];
            $categoryNames = [
                'appointments' => 'Записи',
                'tickets' => 'Тикеты',
                'subscription' => 'Подписки',
            ];
            $categoryIcons = [
                'appointments' => 'fa-calendar-check',
                'tickets' => 'fa-ticket',
                'subscription' => 'fa-crown',
            ];
            if (isset($all['appointments'])) {
                $typesByCategory['appointments'] = $all['appointments'];
            }
            if (isset($all['tickets'])) {
                $typesByCategory['tickets'] = $all['tickets'];
            }
            foreach ($adminSub as $key => $data) {
                $typesByCategory[$key] = $data['types'];
                $categoryNames[$key] = $data['label'];
                $categoryIcons[$key] = $data['icon'];
            }
            if (isset($all['subscription'])) {
                $typesByCategory['subscription'] = $all['subscription'];
            }
        }

        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();
        $botUsername = $bot ? $bot->name : null;
        if (empty($user->telegram_token)) {
            $user->telegram_token = Str::random(32);
            $user->save();
        }
        $telegramLink = $botUsername && $user->telegram_token
            ? "https://t.me/{$botUsername}?start=user_auth_{$user->telegram_token}"
            : null;

        return view('panel.settings.notifications.index', compact(
            'settings', 'channels', 'typesByCategory', 'categoryNames', 'categoryIcons',
            'user', 'telegramLink'
        ));
    }

    /**
     * Обновить настройку (AJAX).
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
     * Получить настройки (JSON API).
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
     * Отвязать Telegram.
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
