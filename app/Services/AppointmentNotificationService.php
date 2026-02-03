<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\Client;
use App\Models\User;
use App\Notifications\AppointmentCreated as AppointmentCreatedNotification;
use App\Notifications\AppointmentStatusChanged as AppointmentStatusChangedNotification;
use App\Notifications\AppointmentUpcoming as AppointmentUpcomingNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentNotificationService
{
    /**
     * Статусы, при смене на которые отправляются уведомления (клиенту и сотрудникам).
     * Подтверждена, отменена, завершена — колокольчик, email и Telegram не спамят на любой чих.
     */
    public const NOTIFY_STATUSES = ['confirmed', 'cancelled', 'completed'];

    /**
     * Нужно ли отправить клиенту уведомление об изменении записи.
     */
    public static function shouldNotifyClientOnStatusChange(?string $oldStatus, string $newStatus): bool
    {
        if ($oldStatus === $newStatus) {
            return false;
        }

        return in_array($newStatus, self::NOTIFY_STATUSES, true);
    }

    /**
     * Нужно ли отправить сотрудникам уведомление (колокольчик, email, Telegram).
     * Только при смене на важный статус — иначе история в колокольчике и почта забиваются.
     */
    public static function shouldNotifyStaffOnStatusChange(?string $oldStatus, string $newStatus): bool
    {
        if ($oldStatus === $newStatus) {
            return false;
        }

        return in_array($newStatus, self::NOTIFY_STATUSES, true);
    }

    /**
     * Получить правильный маршрут для просмотра записи в зависимости от прав пользователя
     */
    protected static function getAppointmentRoute(User $user, Appointment $appointment): string
    {
        $business = $appointment->business;

        // Проверяем бизнес-права пользователя в этом бизнесе
        $pivotData = DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();

        $hasBusinessPermission = false;

        if ($pivotData) {
            // Получаем роль
            if ($pivotData->role_id) {
                $role = BusinessRole::find($pivotData->role_id);
            } elseif ($pivotData->role) {
                $role = BusinessRole::where('slug', $pivotData->role)->first();
            } else {
                $role = null;
            }

            if ($role) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                // Owner всегда имеет все права
                if ($role->slug === 'owner') {
                    $hasBusinessPermission = true;
                } else {
                    // Проверяем бизнес-права на просмотр записей
                    $hasBusinessPermission = $permissionService->hasPermission($role->id, 'client.appointments.view');
                }
            }
        }

        // Если у пользователя есть бизнес-права в этом бизнесе - используем клиентский маршрут
        if ($pivotData && $hasBusinessPermission) {
            return route('appointments.show', $appointment);
        }

        // Если нет бизнес-прав, но есть административные права - используем админский маршрут (edit)
        if (! $pivotData && $user->can('panel.appointments.view')) {
            return route('panel.appointments.edit', $appointment);
        }

        // По умолчанию - клиентский маршрут
        return route('appointments.show', $appointment);
    }

    /**
     * Получить правильный маршрут для просмотра клиента в зависимости от прав пользователя
     */
    protected static function getClientRoute(User $user, Client $client): string
    {
        $business = $client->business;

        // Проверяем бизнес-права пользователя в этом бизнесе
        $pivotData = DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();

        $hasBusinessPermission = false;

        if ($pivotData) {
            // Получаем роль
            if ($pivotData->role_id) {
                $role = BusinessRole::find($pivotData->role_id);
            } elseif ($pivotData->role) {
                $role = BusinessRole::where('slug', $pivotData->role)->first();
            } else {
                $role = null;
            }

            if ($role) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);
                // Owner всегда имеет все права
                if ($role->slug === 'owner') {
                    $hasBusinessPermission = true;
                } else {
                    // Проверяем бизнес-права на просмотр клиентов
                    $hasBusinessPermission = $permissionService->hasPermission($role->id, 'client.clients.view');
                }
            }
        }

        // Если у пользователя есть бизнес-права в этом бизнесе - используем клиентский маршрут
        if ($pivotData && $hasBusinessPermission) {
            return route('clients.show', $client);
        }

        // Если нет бизнес-прав, но есть административные права - используем админский маршрут (edit)
        if (! $pivotData && $user->can('panel.clients.view')) {
            return route('panel.clients.edit', $client);
        }

        // По умолчанию - клиентский маршрут
        return route('clients.show', $client);
    }

    /**
     * Отправить уведомление о создании записи
     */
    public static function notifyCreated(Appointment $appointment): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $appointment->loadMissing(['business.users', 'client', 'service']);

        $business = $appointment->business;
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        // Получаем пользователей бизнеса с правами просмотра записей
        $users = $business->users;

        foreach ($users as $user) {
            // Получаем роль пользователя в бизнесе
            $pivotData = DB::table('business_user')
                ->where('user_id', $user->id)
                ->where('business_id', $business->id)
                ->first();

            $hasPermission = false;

            if ($pivotData) {
                // Получаем роль
                if ($pivotData->role_id) {
                    $role = BusinessRole::find($pivotData->role_id);
                } elseif ($pivotData->role) {
                    $role = BusinessRole::where('slug', $pivotData->role)->first();
                } else {
                    $role = null;
                }

                if ($role) {
                    // Owner всегда имеет все права
                    if ($role->slug === 'owner') {
                        $hasPermission = true;
                    } else {
                        // Проверяем бизнес-права на просмотр записей
                        $hasPermission = $permissionService->hasPermission($role->id, 'client.appointments.view');
                    }
                }
            }

            // Также проверяем административные права
            $hasPanelPermission = $user->can('panel.appointments.view');

            // Пропускаем пользователей без прав
            if (! $hasPermission && ! $hasPanelPermission) {
                continue;
            }

            if (! NotificationSettingsService::isTypeEnabled($user, 'appointment.created')) {
                continue;
            }

            // Системное уведомление
            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'appointment.created',
                'title' => 'Новая запись',
                'message' => sprintf(
                    'Запись: %s %s %s',
                    $appointment->client->first_name ?? 'Клиент',
                    $appointment->service->name ?? '',
                    $appointment->date->format('d.m.Y')
                ),
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'appointment_id' => $appointment->id,
                    'url' => self::getAppointmentRoute($user, $appointment),
                ],
            ]);

            // Email уведомление (если включено в настройках)
            if (! empty($user->email) && NotificationSettingsService::shouldSendEmail($user, 'appointment.created')) {
                try {
                    $user->notify(new AppointmentCreatedNotification($appointment));
                } catch (\Exception $e) {
                    Log::error('Failed to send email notification for appointment.created', [
                        'user_id' => $user->id,
                        'appointment_id' => $appointment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Telegram уведомление (если включено в настройках)
            if (NotificationSettingsService::shouldSendTelegram($user, 'appointment.created')) {
                try {
                    TelegramNotificationService::sendAppointmentCreated($appointment, $user);
                } catch (\Exception $e) {
                    Log::error('Failed to send telegram notification for appointment.created', [
                        'user_id' => $user->id,
                        'appointment_id' => $appointment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Отправить уведомление об изменении статуса записи сотрудникам (in-app, email, Telegram).
     *
     * Вызывать только при реальной смене статуса. Проверку выполняет вызывающий код через
     * shouldNotifyStaffOnStatusChange(). Клиенту уведомление шлётся отдельно только при
     * переходах в confirmed/cancelled/completed (shouldNotifyClientOnStatusChange).
     */
    public static function notifyStatusChanged(Appointment $appointment, ?string $oldStatus = null): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $appointment->loadMissing(['business.users', 'client', 'service']);

        $business = $appointment->business;
        $users = $business->users;
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        $statusText = match ($appointment->status) {
            'confirmed' => 'подтверждена',
            'cancelled' => 'отменена',
            'completed' => 'завершена',
            default => 'обновлена',
        };

        foreach ($users as $user) {
            // Получаем роль пользователя в бизнесе
            $pivotData = DB::table('business_user')
                ->where('user_id', $user->id)
                ->where('business_id', $business->id)
                ->first();

            $hasPermission = false;

            if ($pivotData) {
                // Получаем роль
                if ($pivotData->role_id) {
                    $role = BusinessRole::find($pivotData->role_id);
                } elseif ($pivotData->role) {
                    $role = BusinessRole::where('slug', $pivotData->role)->first();
                } else {
                    $role = null;
                }

                if ($role) {
                    // Owner всегда имеет все права
                    if ($role->slug === 'owner') {
                        $hasPermission = true;
                    } else {
                        // Проверяем бизнес-права на просмотр записей
                        $hasPermission = $permissionService->hasPermission($role->id, 'client.appointments.view');
                    }
                }
            }

            // Также проверяем административные права
            $hasPanelPermission = $user->can('panel.appointments.view');

            // Пропускаем пользователей без прав
            if (! $hasPermission && ! $hasPanelPermission) {
                continue;
            }

            if (! NotificationSettingsService::isTypeEnabled($user, 'appointment.status_changed')) {
                continue;
            }

            // Системное уведомление
            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'appointment.status_changed',
                'title' => 'Запись '.$statusText,
                'message' => sprintf(
                    '%s %s %s - %s',
                    $appointment->client->first_name ?? 'Клиент',
                    $appointment->service->name ?? '',
                    $appointment->date->format('d.m.Y'),
                    $statusText
                ),
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'appointment_id' => $appointment->id,
                    'url' => self::getAppointmentRoute($user, $appointment),
                ],
            ]);

            // Email уведомление (если включено в настройках)
            if (! empty($user->email) && NotificationSettingsService::shouldSendEmail($user, 'appointment.status_changed')) {
                try {
                    $user->notify(new AppointmentStatusChangedNotification($appointment, $oldStatus));
                } catch (\Exception $e) {
                    Log::error('Failed to send email notification for appointment.status_changed', [
                        'user_id' => $user->id,
                        'appointment_id' => $appointment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Telegram уведомление (если включено в настройках)
            if (NotificationSettingsService::shouldSendTelegram($user, 'appointment.status_changed')) {
                try {
                    TelegramNotificationService::sendAppointmentStatusChanged($appointment, $user, $oldStatus);
                } catch (\Exception $e) {
                    Log::error('Failed to send telegram notification for appointment.status_changed', [
                        'user_id' => $user->id,
                        'appointment_id' => $appointment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Отправить уведомление о приближении записи
     */
    public static function notifyUpcoming(Appointment $appointment): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $appointment->loadMissing(['business.users', 'client', 'service']);

        $business = $appointment->business;
        $users = $business->users;
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        foreach ($users as $user) {
            // Получаем роль пользователя в бизнесе
            $pivotData = DB::table('business_user')
                ->where('user_id', $user->id)
                ->where('business_id', $business->id)
                ->first();

            $hasPermission = false;

            if ($pivotData) {
                // Получаем роль
                if ($pivotData->role_id) {
                    $role = BusinessRole::find($pivotData->role_id);
                } elseif ($pivotData->role) {
                    $role = BusinessRole::where('slug', $pivotData->role)->first();
                } else {
                    $role = null;
                }

                if ($role) {
                    // Owner всегда имеет все права
                    if ($role->slug === 'owner') {
                        $hasPermission = true;
                    } else {
                        // Проверяем бизнес-права на просмотр записей
                        $hasPermission = $permissionService->hasPermission($role->id, 'client.appointments.view');
                    }
                }
            }

            // Также проверяем административные права
            $hasPanelPermission = $user->can('panel.appointments.view');

            // Пропускаем пользователей без прав
            if (! $hasPermission && ! $hasPanelPermission) {
                continue;
            }

            if (! NotificationSettingsService::isTypeEnabled($user, 'appointment.upcoming')) {
                continue;
            }

            // Системное уведомление
            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'appointment.upcoming',
                'title' => 'Приближается запись',
                'message' => sprintf(
                    'Запись через час: %s %s %s',
                    $appointment->client->first_name ?? 'Клиент',
                    $appointment->service->name ?? '',
                    $appointment->time
                ),
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'appointment_id' => $appointment->id,
                    'url' => self::getAppointmentRoute($user, $appointment),
                ],
            ]);

            // Email уведомление (если включено в настройках)
            if (! empty($user->email) && NotificationSettingsService::shouldSendEmail($user, 'appointment.upcoming')) {
                try {
                    $user->notify(new AppointmentUpcomingNotification($appointment));
                } catch (\Exception $e) {
                    Log::error('Failed to send email notification for appointment.upcoming', [
                        'user_id' => $user->id,
                        'appointment_id' => $appointment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Отправить уведомление о превышении лимита подписки
     */
    public static function notifySubscriptionLimit(Business $business, string $limitType): void
    {
        // Загружаем пользователей бизнеса
        $business->loadMissing('users');
        $users = $business->users;
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        $titles = [
            'max_appointments_per_month' => 'Достигнут лимит записей',
            'max_clients' => 'Достигнут лимит клиентов',
            'max_users' => 'Достигнут лимит пользователей',
        ];

        $messages = [
            'max_appointments_per_month' => 'Вы превысили лимит записей в этом месяце. Обновите подписку для продолжения.',
            'max_clients' => 'Вы превысили лимит клиентов. Обновите подписку для добавления новых.',
            'max_users' => 'Вы превысили лимит пользователей. Обновите подписку для приглашения новых.',
        ];

        foreach ($users as $user) {
            // Получаем роль пользователя в бизнесе
            $pivotData = DB::table('business_user')
                ->where('user_id', $user->id)
                ->where('business_id', $business->id)
                ->first();

            $hasPermission = false;

            if ($pivotData) {
                // Получаем роль
                if ($pivotData->role_id) {
                    $role = BusinessRole::find($pivotData->role_id);
                } elseif ($pivotData->role) {
                    $role = BusinessRole::where('slug', $pivotData->role)->first();
                } else {
                    $role = null;
                }

                if ($role) {
                    // Owner всегда имеет все права
                    if ($role->slug === 'owner') {
                        $hasPermission = true;
                    } else {
                        // Проверяем бизнес-права на просмотр настроек подписки
                        $hasPermission = $permissionService->hasPermission($role->id, 'client.subscription.view');
                    }
                }
            }

            // Также проверяем административные права
            $hasPanelPermission = $user->can('panel.settings.view');

            // Пропускаем пользователей без прав
            if (! $hasPermission && ! $hasPanelPermission) {
                continue;
            }

            if (! NotificationSettingsService::isTypeEnabled($user, 'subscription.limit')) {
                continue;
            }

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'subscription.limit',
                'title' => $titles[$limitType] ?? 'Лимит достигнут',
                'message' => $messages[$limitType] ?? 'Достигнут один из лимитов подписки',
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'url' => route('settings.subscription'),
                ],
            ]);
        }
    }

    /**
     * Отправить уведомление о скором окончании подписки
     */
    public static function notifySubscriptionExpiring(Business $business): void
    {
        // Загружаем пользователей бизнеса
        $business->loadMissing('users');
        $users = $business->users;
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        foreach ($users as $user) {
            // Получаем роль пользователя в бизнесе
            $pivotData = DB::table('business_user')
                ->where('user_id', $user->id)
                ->where('business_id', $business->id)
                ->first();

            $hasPermission = false;

            if ($pivotData) {
                // Получаем роль
                if ($pivotData->role_id) {
                    $role = BusinessRole::find($pivotData->role_id);
                } elseif ($pivotData->role) {
                    $role = BusinessRole::where('slug', $pivotData->role)->first();
                } else {
                    $role = null;
                }

                if ($role) {
                    // Owner всегда имеет все права
                    if ($role->slug === 'owner') {
                        $hasPermission = true;
                    } else {
                        // Проверяем бизнес-права на просмотр настроек подписки
                        $hasPermission = $permissionService->hasPermission($role->id, 'client.subscription.view');
                    }
                }
            }

            // Также проверяем административные права
            $hasPanelPermission = $user->can('panel.settings.view');

            // Пропускаем пользователей без прав
            if (! $hasPermission && ! $hasPanelPermission) {
                continue;
            }

            $subscription = $user->activeSubscription();

            if (! $subscription) {
                continue;
            }

            $daysLeft = $subscription->ends_at
                ? $subscription->ends_at->diffInDays(now())
                : null;

            if ($daysLeft === null || $daysLeft > 7) {
                continue;
            }

            if (! NotificationSettingsService::isTypeEnabled($user, 'subscription.expiring')) {
                continue;
            }

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'subscription.expiring',
                'title' => 'Подписка истекает',
                'message' => $daysLeft > 0
                    ? "Ваша подписка истекает через {$daysLeft} дн. Обновите для непрерывной работы."
                    : 'Подписка истекла сегодня. Обновите для восстановления доступа.',
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'subscription_id' => $subscription->id,
                    'url' => route('settings.subscription'),
                ],
            ]);
        }
    }

    /**
     * Отправить уведомление о новом клиенте
     */
    public static function notifyNewClient(Appointment $appointment): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $appointment->loadMissing(['business.users', 'client', 'service']);

        $business = $appointment->business;
        $users = $business->users;
        $permissionService = app(\App\Services\BusinessRolePermissionService::class);

        foreach ($users as $user) {
            // Получаем роль пользователя в бизнесе
            $pivotData = DB::table('business_user')
                ->where('user_id', $user->id)
                ->where('business_id', $business->id)
                ->first();

            $hasPermission = false;

            if ($pivotData) {
                // Получаем роль
                if ($pivotData->role_id) {
                    $role = BusinessRole::find($pivotData->role_id);
                } elseif ($pivotData->role) {
                    $role = BusinessRole::where('slug', $pivotData->role)->first();
                } else {
                    $role = null;
                }

                if ($role) {
                    // Owner всегда имеет все права
                    if ($role->slug === 'owner') {
                        $hasPermission = true;
                    } else {
                        // Проверяем бизнес-права на просмотр клиентов
                        $hasPermission = $permissionService->hasPermission($role->id, 'client.clients.view');
                    }
                }
            }

            // Также проверяем административные права
            $hasPanelPermission = $user->can('panel.clients.view');

            // Пропускаем пользователей без прав
            if (! $hasPermission && ! $hasPanelPermission) {
                continue;
            }

            if (! NotificationSettingsService::isTypeEnabled($user, 'client.new')) {
                continue;
            }

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'client.new',
                'title' => 'Новый клиент',
                'message' => sprintf(
                    '%s записался на %s',
                    $appointment->client->first_name ?? 'Клиент',
                    $appointment->service->name ?? 'услугу'
                ),
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'client_id' => $appointment->client->id,
                    'url' => self::getClientRoute($user, $appointment->client),
                ],
            ]);
        }
    }
}
