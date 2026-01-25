<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Support\Facades\Log;

class NotificationSettingsService
{
    /**
     * Проверить, включён ли тип уведомлений для пользователя.
     * Если отключён — не отправлять ничего (in-app, email, telegram).
     * Нет настройки = по умолчанию включено.
     */
    public static function isTypeEnabled(User $user, string $notificationType): bool
    {
        $setting = UserNotificationSetting::where('user_id', $user->id)
            ->where('notification_type', $notificationType)
            ->first();

        if (! $setting) {
            return true;
        }

        return $setting->isEnabled();
    }

    /**
     * Универсальный метод проверки, нужно ли отправлять уведомление через указанный канал.
     * Если тип отключён (enabled=false), всегда false.
     */
    public static function shouldSend(User $user, string $notificationType, string $channel): bool
    {
        if (! self::isTypeEnabled($user, $notificationType)) {
            return false;
        }

        $setting = UserNotificationSetting::where('user_id', $user->id)
            ->where('notification_type', $notificationType)
            ->first();

        if (! $setting) {
            return true;
        }

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
                'enabled' => $setting ? $setting->isEnabled() : true,
                'channels' => $setting ? $setting->channels : $defaultChannels,
            ];
        }

        return $settings;
    }

    /**
     * Обновить настройку для пользователя.
     *
     * @param  array<string, bool>  $channels
     */
    public static function updateSetting(User $user, string $notificationType, array $channels, ?bool $enabled = null): UserNotificationSetting
    {
        $setting = UserNotificationSetting::getForUser($user, $notificationType);

        $data = ['channels' => $channels];
        if ($enabled !== null) {
            $data['enabled'] = $enabled;
        }
        $setting->update($data);

        Log::info('NotificationSettingsService: Setting updated', [
            'user_id' => $user->id,
            'notification_type' => $notificationType,
            'enabled' => $enabled,
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
                'enabled' => true,
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
