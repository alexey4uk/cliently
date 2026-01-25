<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Support\Facades\Log;

class NotificationSettingsService
{
    /**
     * Универсальный метод проверки, нужно ли отправлять уведомление через указанный канал.
     */
    public static function shouldSend(User $user, string $notificationType, string $channel): bool
    {
        // Получаем настройку пользователя для данного типа уведомления
        $setting = UserNotificationSetting::where('user_id', $user->id)
            ->where('notification_type', $notificationType)
            ->first();

        // Если настройки нет - по умолчанию все каналы включены
        if (! $setting) {
            return true;
        }

        // Проверяем, включен ли канал в настройках
        return $setting->isChannelEnabled($channel);
    }

    /**
     * Проверить, нужно ли отправлять email уведомление.
     */
    public static function shouldSendEmail(User $user, string $notificationType): bool
    {
        return self::shouldSend($user, $notificationType, 'email');
    }

    /**
     * Проверить, нужно ли отправлять telegram уведомление.
     */
    public static function shouldSendTelegram(User $user, string $notificationType): bool
    {
        return self::shouldSend($user, $notificationType, 'telegram');
    }

    /**
     * Получить все настройки пользователя с дефолтными значениями.
     */
    public static function getUserSettings(User $user): array
    {
        $allTypes = self::getAllNotificationTypes();
        $defaultChannels = self::getDefaultChannels();
        $userSettings = UserNotificationSetting::where('user_id', $user->id)
            ->get()
            ->keyBy('notification_type');

        $settings = [];

        foreach ($allTypes as $type => $name) {
            $setting = $userSettings->get($type);
            $settings[$type] = [
                'name' => $name,
                'channels' => $setting ? $setting->channels : $defaultChannels,
            ];
        }

        return $settings;
    }

    /**
     * Обновить настройку для пользователя.
     */
    public static function updateSetting(User $user, string $notificationType, array $channels): UserNotificationSetting
    {
        $setting = UserNotificationSetting::getForUser($user, $notificationType);
        $setting->updateChannels($channels);

        Log::info('NotificationSettingsService: Setting updated', [
            'user_id' => $user->id,
            'notification_type' => $notificationType,
            'channels' => $channels,
        ]);

        return $setting;
    }

    /**
     * Получить список доступных каналов из конфига.
     */
    public static function getAvailableChannels(): array
    {
        return config('notifications.channels', []);
    }

    /**
     * Получить дефолтные значения для всех каналов.
     */
    public static function getDefaultChannels(): array
    {
        $defaultChannels = config('notifications.default_channels', []);
        $availableChannels = array_keys(self::getAvailableChannels());

        // Убеждаемся, что все доступные каналы имеют дефолтное значение
        $result = [];
        foreach ($availableChannels as $channel) {
            $result[$channel] = $defaultChannels[$channel] ?? true;
        }

        return $result;
    }

    /**
     * Получить список всех типов уведомлений с дефолтными значениями.
     */
    public static function getDefaultSettings(): array
    {
        $allTypes = self::getAllNotificationTypes();
        $defaultChannels = self::getDefaultChannels();

        $settings = [];
        foreach ($allTypes as $type => $name) {
            $settings[$type] = [
                'name' => $name,
                'channels' => $defaultChannels,
            ];
        }

        return $settings;
    }

    /**
     * Получить все типы уведомлений из конфига (плоский список).
     */
    public static function getAllNotificationTypes(): array
    {
        $types = config('notifications.types', []);
        $flatTypes = [];

        foreach ($types as $category => $categoryTypes) {
            foreach ($categoryTypes as $type => $name) {
                $flatTypes[$type] = $name;
            }
        }

        return $flatTypes;
    }

    /**
     * Получить типы уведомлений сгруппированные по категориям.
     */
    public static function getNotificationTypesByCategory(): array
    {
        return config('notifications.types', []);
    }
}
