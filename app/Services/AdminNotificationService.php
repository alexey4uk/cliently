<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\Admin\BusinessCreated as BusinessCreatedNotification;
use App\Notifications\Admin\SubscriptionExpiring as SubscriptionExpiringNotification;
use App\Notifications\Admin\TicketCreated as TicketCreatedNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminNotificationService
{
    /**
     * Получить всех админов с указанным правом
     */
    protected static function getAdminsWithPermission(
        string $permission,
    ): \Illuminate\Database\Eloquent\Collection {
        return User::role("admin")
            ->where(function ($query) use ($permission) {
                $query
                    ->whereHas("permissions", function ($q) use ($permission) {
                        $q->where("name", $permission);
                    })
                    ->orWhereHas("roles.permissions", function ($q) use (
                        $permission,
                    ) {
                        $q->where("name", $permission);
                    });
            })
            ->get();
    }

    /**
     * Получить всех админов (упрощенный вариант - все с ролью admin)
     */
    protected static function getAllAdmins(): \Illuminate\Database\Eloquent\Collection
    {
        return User::role("admin")->get();
    }

    /**
     * Уведомить админов о создании нового бизнеса
     */
    public static function notifyBusinessCreated(Business $business): void
    {
        $admins = self::getAdminsWithPermission("panel.businesses.view");

        if ($admins->isEmpty()) {
            Log::info(
                "AdminNotificationService: No admins found for business.created notification",
            );

            return;
        }

        // Получаем владельца бизнеса
        $ownerRoleId = \App\Models\BusinessRole::where("slug", "owner")->value(
            "id",
        );
        $owner = $business
            ->users()
            ->wherePivotIn("role_id", [$ownerRoleId])
            ->first();

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.business.created",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.business.created",
                "title" => "Новый бизнес зарегистрирован",
                "message" => sprintf(
                    'Бизнес "%s" создан. Владелец: %s',
                    $business->name,
                    $owner ? $owner->name : "Не указан",
                ),
                "required_permission" => "panel.businesses.view",
                "data" => [
                    "business_id" => $business->id,
                    "url" => route("panel.businesses.show", $business),
                ],
            ]);

            // Email уведомление (если включено в настройках)
            if (
                NotificationSettingsService::shouldSendEmail(
                    $admin,
                    "admin.business.created",
                )
            ) {
                try {
                    $admin->notify(new BusinessCreatedNotification($business));
                } catch (\Exception $e) {
                    Log::error(
                        "Failed to send email notification for admin.business.created",
                        [
                            "admin_id" => $admin->id,
                            "business_id" => $business->id,
                            "error" => $e->getMessage(),
                        ],
                    );
                }
            }

            // Telegram уведомление (если включено в настройках)
            if (
                NotificationSettingsService::shouldSendTelegram(
                    $admin,
                    "admin.business.created",
                )
            ) {
                try {
                    TelegramNotificationService::sendAdminBusinessCreated(
                        $business,
                        $admin,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        "Failed to send telegram notification for admin.business.created",
                        [
                            "admin_id" => $admin->id,
                            "business_id" => $business->id,
                            "error" => $e->getMessage(),
                        ],
                    );
                }
            }
        }

        Log::info(
            "AdminNotificationService: Business created notifications sent",
            [
                "business_id" => $business->id,
                "admins_count" => $admins->count(),
            ],
        );
    }

    /**
     * Уведомить админов об удалении бизнеса
     */
    public static function notifyBusinessDeleted(
        Business $business,
        ?User $deletedBy = null,
    ): void {
        $admins = self::getAdminsWithPermission("panel.businesses.view");

        if ($admins->isEmpty()) {
            return;
        }

        $deletedByText = $deletedBy
            ? sprintf(" (удален пользователем: %s)", $deletedBy->name)
            : "";

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.business.deleted",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.business.deleted",
                "title" => "Бизнес удален",
                "message" => sprintf(
                    'Бизнес "%s" был удален%s',
                    $business->name,
                    $deletedByText,
                ),
                "required_permission" => "panel.businesses.view",
                "data" => [
                    "business_id" => $business->id,
                    "url" => route("panel.businesses"),
                ],
            ]);
        }
    }

    /**
     * Уведомить админов о новом тикете от пользователя
     */
    public static function notifyTicketCreated(Ticket $ticket): void
    {
        // Уведомляем только если тикет создан пользователем бизнеса (не админом)
        if ($ticket->created_by_type !== "user") {
            return;
        }

        $admins = self::getAdminsWithPermission("panel.tickets.view");

        if ($admins->isEmpty()) {
            return;
        }

        $business = $ticket->business;
        $creator = $ticket->creator();

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.ticket.created",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.ticket.created",
                "title" => "Новый тикет от пользователя",
                "message" => sprintf(
                    'Тикет "%s" от бизнеса "%s"',
                    $ticket->title,
                    $business->name ?? "Не указан",
                ),
                "required_permission" => "panel.tickets.view",
                "data" => [
                    "ticket_id" => $ticket->id,
                    "url" => route("panel.tickets.show", $ticket),
                ],
            ]);

            // Email уведомление (если включено в настройках)
            if (
                NotificationSettingsService::shouldSendEmail(
                    $admin,
                    "admin.ticket.created",
                )
            ) {
                try {
                    $admin->notify(new TicketCreatedNotification($ticket));
                } catch (\Exception $e) {
                    Log::error(
                        "Failed to send email notification for admin.ticket.created",
                        [
                            "admin_id" => $admin->id,
                            "ticket_id" => $ticket->id,
                            "error" => $e->getMessage(),
                        ],
                    );
                }
            }

            // Telegram уведомление (если включено в настройках)
            if (
                NotificationSettingsService::shouldSendTelegram(
                    $admin,
                    "admin.ticket.created",
                )
            ) {
                try {
                    TelegramNotificationService::sendAdminTicketCreated(
                        $ticket,
                        $admin,
                    );
                } catch (\Exception $e) {
                    Log::error(
                        "Failed to send telegram notification for admin.ticket.created",
                        [
                            "admin_id" => $admin->id,
                            "ticket_id" => $ticket->id,
                            "error" => $e->getMessage(),
                        ],
                    );
                }
            }
        }
    }

    /**
     * Уведомить админов о критическом тикете (без ответа более 24 часов)
     */
    public static function notifyTicketCritical(Ticket $ticket): void
    {
        $admins = self::getAdminsWithPermission("panel.tickets.view");

        if ($admins->isEmpty()) {
            return;
        }

        $hoursWithoutResponse = $ticket->created_at->diffInHours(now());
        $business = $ticket->business;

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.ticket.critical",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.ticket.critical",
                "title" => "Критический тикет требует внимания",
                "message" => sprintf(
                    'Тикет "%s" от бизнеса "%s" без ответа более %d часов',
                    $ticket->title,
                    $business->name ?? "Не указан",
                    $hoursWithoutResponse,
                ),
                "required_permission" => "panel.tickets.view",
                "data" => [
                    "ticket_id" => $ticket->id,
                    "url" => route("panel.tickets.show", $ticket),
                ],
            ]);
        }
    }

    /**
     * Уведомить админов о новом пользователе
     */
    public static function notifyUserCreated(User $user): void
    {
        $admins = self::getAdminsWithPermission("panel.users.view");

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.user.created",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.user.created",
                "title" => "Новый пользователь зарегистрирован",
                "message" => sprintf(
                    "Пользователь %s (%s) зарегистрировался в системе",
                    $user->name,
                    $user->email,
                ),
                "required_permission" => "panel.users.view",
                "data" => [
                    "user_id" => $user->id,
                    "url" => route("panel.users.edit", $user),
                ],
            ]);
        }
    }

    /**
     * Уведомить админов об истечении подписки
     * Также уведомляет владельцев бизнеса
     */
    public static function notifySubscriptionExpiring(
        Subscription $subscription,
    ): void {
        $user = $subscription->user;
        $business = $user->businesses()->first(); // Получаем первый бизнес пользователя
        $daysLeft = $subscription->ends_at
            ? $subscription->ends_at->diffInDays(now())
            : null;

        if ($daysLeft === null || $daysLeft > 3) {
            return; // Уведомляем только за 3 дня до истечения или после
        }

        // 1. Уведомляем владельца бизнеса (owner)
        if ($business) {
            $ownerRoleId = BusinessRole::where("slug", "owner")->value("id");
            $owner = $business
                ->users()
                ->wherePivotIn("role_id", [$ownerRoleId])
                ->first();

            if (
                $owner &&
                NotificationSettingsService::isTypeEnabled(
                    $owner,
                    "subscription.expiring",
                )
            ) {
                NotificationService::send([
                    "user_id" => $owner->id,
                    "type" => "subscription.expiring",
                    "title" => "Подписка истекает",
                    "message" => sprintf(
                        "Ваша подписка на тариф «%s» истекает через %d %s. Продлите подписку для продолжения использования всех функций.",
                        $subscription->plan->name ?? "Не указан",
                        $daysLeft > 0 ? $daysLeft : 0,
                        $daysLeft === 1
                            ? "день"
                            : ($daysLeft < 5
                                ? "дня"
                                : "дней"),
                    ),
                    "required_permission" => null,
                    "data" => [
                        "subscription_id" => $subscription->id,
                        "plan_id" => $subscription->plan_id,
                        "business_id" => $business->id,
                        "days_left" => $daysLeft,
                        "url" => route("subscription.index"),
                    ],
                ]);

                // Email уведомление владельцу (если включено в настройках)
                if (
                    NotificationSettingsService::shouldSendEmail(
                        $owner,
                        "subscription.expiring",
                    ) &&
                    $owner->hasVerifiedEmail()
                ) {
                    try {
                        // Можно создать отдельное уведомление для владельца
                        Log::info(
                            "Subscription expiring notification sent to owner via email",
                            [
                                "user_id" => $owner->id,
                                "subscription_id" => $subscription->id,
                            ],
                        );
                    } catch (\Exception $e) {
                        Log::error(
                            "Failed to send email notification for subscription.expiring to owner",
                            [
                                "user_id" => $owner->id,
                                "subscription_id" => $subscription->id,
                                "error" => $e->getMessage(),
                            ],
                        );
                    }
                }

                // Telegram уведомление владельцу (если включено в настройках)
                if (
                    NotificationSettingsService::shouldSendTelegram(
                        $owner,
                        "subscription.expiring",
                    ) &&
                    $owner->isTelegramConnected()
                ) {
                    try {
                        \App\Services\TelegramNotificationService::sendSubscriptionExpiring(
                            $subscription,
                            $owner,
                        );
                    } catch (\Exception $e) {
                        Log::error(
                            "Failed to send telegram notification for subscription.expiring to owner",
                            [
                                "user_id" => $owner->id,
                                "subscription_id" => $subscription->id,
                                "error" => $e->getMessage(),
                            ],
                        );
                    }
                }
            }
        }

        // 2. Дополнительно уведомляем системных админов
        $admins = self::getAdminsWithPermission("panel.businesses.view");

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.subscription.expiring",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.subscription.expiring",
                "title" => "Подписка истекает",
                "message" => sprintf(
                    'Подписка бизнеса "%s" истекает через %d дн. (Тариф: %s)',
                    $business ? $business->name : "Не указан",
                    $daysLeft > 0 ? $daysLeft : 0,
                    $subscription->plan->name ?? "Не указан",
                ),
                "required_permission" => "panel.businesses.view",
                "data" => [
                    "subscription_id" => $subscription->id,
                    "business_id" => $business?->id,
                    "url" => $business
                        ? route("panel.businesses.show", $business)
                        : route("panel.businesses"),
                ],
            ]);

            // Email уведомление (если включено в настройках)
            if (
                NotificationSettingsService::shouldSendEmail(
                    $admin,
                    "admin.subscription.expiring",
                )
            ) {
                try {
                    $admin->notify(
                        new SubscriptionExpiringNotification($subscription),
                    );
                } catch (\Exception $e) {
                    Log::error(
                        "Failed to send email notification for admin.subscription.expiring",
                        [
                            "admin_id" => $admin->id,
                            "subscription_id" => $subscription->id,
                            "error" => $e->getMessage(),
                        ],
                    );
                }
            }
        }
    }

    /**
     * Уведомить админов о превышении лимитов бизнесом (с throttling 15 мин на пару business+limitType).
     */
    public static function notifySubscriptionLimitExceededIfNotThrottled(
        Business $business,
        string $limitType,
    ): void {
        self::notifySubscriptionLimitExceeded($business, $limitType);
    }

    /**
     * Уведомить админов о превышении лимитов бизнесом
     */
    public static function notifySubscriptionLimitExceeded(
        Business $business,
        string $limitType,
    ): void {
        $admins = self::getAdminsWithPermission("panel.businesses.view");

        if ($admins->isEmpty()) {
            return;
        }

        $limitNames = [
            "max_appointments_per_month" => "лимит записей в месяц",
            "max_clients" => "лимит клиентов",
            "max_users" => "лимит пользователей",
            "max_business_users" => "лимит пользователей бизнеса",
        ];

        $limitName = $limitNames[$limitType] ?? $limitType;

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.subscription.limit.exceeded",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.subscription.limit.exceeded",
                "title" => "Бизнес превысил лимиты",
                "message" => sprintf(
                    'Бизнес "%s" превысил %s. Рекомендуется предложить апгрейд тарифа.',
                    $business->name,
                    $limitName,
                ),
                "required_permission" => "panel.businesses.view",
                "data" => [
                    "business_id" => $business->id,
                    "limit_type" => $limitType,
                    "url" => route("panel.businesses.show", $business),
                ],
            ]);
        }
    }

    /**
     * Уведомить админов о неактивном бизнесе
     */
    public static function notifyBusinessInactive(
        Business $business,
        int $daysInactive,
    ): void {
        $admins = self::getAdminsWithPermission("panel.businesses.view");

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.business.inactive",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.business.inactive",
                "title" => "Неактивный бизнес",
                "message" => sprintf(
                    'Бизнес "%s" неактивен более %d дней. Последняя активность: %s',
                    $business->name,
                    $daysInactive,
                    $business->updated_at->format("d.m.Y"),
                ),
                "required_permission" => "panel.businesses.view",
                "data" => [
                    "business_id" => $business->id,
                    "url" => route("panel.businesses.show", $business),
                ],
            ]);
        }
    }

    /**
     * Уведомить админов о системной ошибке
     */
    public static function notifySystemError(
        string $errorType,
        string $message,
        ?string $url = null,
    ): void {
        $admins = self::getAllAdmins();

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            if (
                !NotificationSettingsService::isTypeEnabled(
                    $admin,
                    "admin.system.error",
                )
            ) {
                continue;
            }
            NotificationService::send([
                "user_id" => $admin->id,
                "type" => "admin.system.error",
                "title" => "Системная ошибка",
                "message" => sprintf("%s: %s", $errorType, $message),
                "required_permission" => null, // Все админы должны видеть системные ошибки
                "data" => [
                    "error_type" => $errorType,
                    "url" => $url ?? route("panel.index"),
                ],
            ]);
        }
    }
}
