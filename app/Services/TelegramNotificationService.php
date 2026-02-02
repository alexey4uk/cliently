<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Отправить уведомление о новом назначении конкретному пользователю
     */
    public static function sendAppointmentCreated(
        Appointment $appointment,
        User $user,
    ) {
        if (!$user->telegram_chat_id) {
            return;
        }

        $message = self::formatAppointmentMessage($appointment, "новая запись");

        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление об изменении статуса назначения конкретному пользователю
     */
    public static function sendAppointmentStatusChanged(
        Appointment $appointment,
        User $user,
        ?string $oldStatus = null,
    ) {
        if (!$user->telegram_chat_id) {
            return;
        }

        $statusText = match ($appointment->status) {
            "confirmed" => "подтверждена",
            "cancelled" => "отменена",
            "completed" => "завершена",
            default => "обновлена",
        };

        $message = self::formatAppointmentMessage(
            $appointment,
            "запись {$statusText}",
        );

        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление об изменении статуса назначения для клиента
     */
    public static function sendAppointmentStatusChangedForClient(
        Appointment $appointment,
        ?string $oldStatus = null,
    ) {
        // Проверяем, есть ли у клиента telegram_user_id
        if (!$appointment->client->telegram_user_id) {
            return;
        }

        $statusText = match ($appointment->status) {
            "confirmed" => "подтверждена",
            "cancelled" => "отменена",
            "completed" => "завершена",
            default => "обновлена",
        };

        $message = self::formatAppointmentMessage(
            $appointment,
            "запись {$statusText}",
        );

        self::sendMessageForClient(
            $appointment->client->telegram_user_id,
            $message,
        );
    }

    /**
     * Форматировать сообщение о назначении
     */
    private static function formatAppointmentMessage(
        Appointment $appointment,
        string $action,
    ): string {
        $client = $appointment->client;
        $service = $appointment->service;
        $master = $appointment->master;
        $location = $appointment->location;

        $message = "📅 {$action}\n\n";
        $message .= "👤 Клиент: {$client->first_name} {$client->last_name}\n";
        $message .= "📱 Телефон: {$client->phone}\n";
        $message .= "💼 Услуга: {$service->name}\n";

        if ($master) {
            $message .= "👨‍💼 Мастер: {$master->first_name} {$master->last_name}\n";
        }

        if ($location) {
            $message .= "📍 Локация: {$location->name}\n";
        }

        $message .= "📆 Дата: {$appointment->date->format("d.m.Y")}\n";
        $message .= "🕐 Время: {$appointment->time}\n";

        if ($appointment->price) {
            $message .= "💰 Цена: {$appointment->price} BYN\n";
        }

        if ($appointment->notes) {
            $message .= "📝 Заметки: {$appointment->notes}\n";
        }

        return $message;
    }

    /**
     * Отправить рассылку (broadcast) пользователю в Telegram.
     * Только для пользователей с привязанным аккаунтом (isTelegramConnected).
     */
    public static function sendBroadcastToUser(
        User $user,
        string $title,
        string $message,
    ): void {
        if (!$user->isTelegramConnected()) {
            return;
        }

        $text = "📢 {$title}\n\n{$message}";
        self::sendMessageToUser($user, $text);
    }

    /**
     * Отправить сообщение в личный чат пользователя
     */
    private static function sendMessageToUser(User $user, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (!$bot) {
                return;
            }

            if (!$user->telegram_chat_id) {
                return;
            }

            $chat = TelegraphChat::where(
                "chat_id",
                $user->telegram_chat_id,
            )->first();

            if (!$chat) {
                $chat = $bot->chats()->create([
                    "chat_id" => $user->telegram_chat_id,
                    "name" => $user->name ?? "User Notifications",
                ]);
            }

            $chat->message($message)->send();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error(
                "Telegram notification failed for user: " . $e->getMessage(),
                [
                    "user_id" => $user->id,
                    "chat_id" => $user->telegram_chat_id,
                ],
            );
        }
    }

    /**
     * Отправить сообщение в Telegram (для бизнеса - оставлено для обратной совместимости)
     *
     * @deprecated Используйте sendMessageToUser для отправки пользователям
     */
    private static function sendMessage(Business $business, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (!$bot) {
                return;
            }

            if (!$business->telegram_chat_id) {
                return;
            }

            $chat = TelegraphChat::where(
                "chat_id",
                $business->telegram_chat_id,
            )->first();

            if (!$chat) {
                $chat = $bot->chats()->create([
                    "chat_id" => $business->telegram_chat_id,
                    "name" => "Business Notifications",
                ]);
            }

            $chat->message($message)->send();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error("Telegram notification failed: " . $e->getMessage());
        }
    }

    private static function sendMessageForClient(int $id, string $message)
    {
        try {
            $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();

            if (!$bot) {
                return;
            }

            $chat = TelegraphChat::where("chat_id", $id)->first();

            if (!$chat) {
                $chat = $bot->chats()->create([
                    "chat_id" => $id,
                    "name" => "Business Notifications",
                ]);
            }

            $chat->message($message)->send();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error("Telegram notification failed: " . $e->getMessage());
        }
    }

    /**
     * Отправить уведомление о создании тикета конкретному пользователю
     */
    public static function sendTicketCreated(Ticket $ticket, User $user): void
    {
        if (!$user->telegram_chat_id) {
            return;
        }

        $message = self::formatTicketMessage($ticket, "новый тикет");
        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление о новом комментарии к тикету конкретному пользователю
     */
    public static function sendTicketCommentAdded(
        Ticket $ticket,
        TicketComment $comment,
        User $user,
    ): void {
        if (!$user->telegram_chat_id) {
            return;
        }

        $commentAuthor = $comment->user;
        $message = "💬 Новый комментарий к тикету #{$ticket->id}\n\n";
        $message .= "📋 Тикет: {$ticket->title}\n";
        $message .=
            "👤 Автор: " . ($commentAuthor->name ?? "Пользователь") . "\n";
        $message .= "💬 Комментарий: " . substr($comment->content, 0, 200);
        if (strlen($comment->content) > 200) {
            $message .= "...";
        }

        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление об изменении статуса тикета конкретному пользователю
     */
    public static function sendTicketStatusChanged(
        Ticket $ticket,
        User $user,
        string $oldStatus,
        string $newStatus,
    ): void {
        if (!$user->telegram_chat_id) {
            return;
        }

        $statusText = match ($newStatus) {
            "pending" => "ожидает",
            "open" => "в работе",
            "completed" => "выполнен",
            "cancelled" => "отменен",
            default => "обновлен",
        };

        $message = self::formatTicketMessage($ticket, "тикет {$statusText}");
        self::sendMessageToUser($user, $message);
    }

    /**
     * Отправить уведомление о назначении тикета конкретному пользователю
     */
    public static function sendTicketAssigned(Ticket $ticket, User $user): void
    {
        if (!$user->telegram_chat_id) {
            return;
        }

        $message = "👤 Вам назначен тикет #{$ticket->id}\n\n";
        $message .= "📋 {$ticket->title}\n";
        if ($ticket->description) {
            $message .= "📝 " . substr($ticket->description, 0, 200);
            if (strlen($ticket->description) > 200) {
                $message .= "...";
            }
            $message .= "\n";
        }

        self::sendMessageToUser($user, $message);
    }

    /**
     * Форматировать сообщение о тикете
     */
    private static function formatTicketMessage(
        Ticket $ticket,
        string $action,
    ): string {
        $message = "🎫 {$action}\n\n";
        $message .= "📋 Тикет #{$ticket->id}: {$ticket->title}\n";

        if ($ticket->description) {
            $message .= "📝 " . substr($ticket->description, 0, 200);
            if (strlen($ticket->description) > 200) {
                $message .= "...";
            }
            $message .= "\n";
        }

        $statusText = match ($ticket->status) {
            "pending" => "⏳ Ожидает",
            "open" => "🔄 В работе",
            "completed" => "✅ Выполнен",
            "cancelled" => "❌ Отменен",
            default => "📌 " . ucfirst($ticket->status),
        };
        $message .= "📊 Статус: {$statusText}\n";

        if ($ticket->assignedUser) {
            $message .= "👤 Назначен: {$ticket->assignedUser->name}\n";
        }

        return $message;
    }

    /**
     * Отправить админское уведомление о создании бизнеса конкретному админу
     */
    public static function sendAdminBusinessCreated(
        Business $business,
        User $admin,
    ): void {
        if (!$admin->telegram_chat_id) {
            return;
        }

        $ownerRoleId = \App\Models\BusinessRole::where("slug", "owner")->value(
            "id",
        );
        $owner = $business
            ->users()
            ->wherePivotIn("role_id", [$ownerRoleId])
            ->first();
        $message = "🏢 Новый бизнес зарегистрирован\n\n";
        $message .= "📋 Название: {$business->name}\n";
        $message .=
            "👤 Владелец: " . ($owner ? $owner->name : "Не указан") . "\n";
        $message .=
            "📧 Email: " . ($owner ? $owner->email : "Не указан") . "\n";

        self::sendMessageToUser($admin, $message);
    }

    /**
     * Отправить админское уведомление о создании тикета конкретному админу
     */
    public static function sendAdminTicketCreated(
        Ticket $ticket,
        User $admin,
    ): void {
        if (!$admin->telegram_chat_id) {
            return;
        }

        $business = $ticket->business;
        $creator = $ticket->creator();

        $message = "🎫 Новый тикет от пользователя\n\n";
        $message .= "📋 Тикет #{$ticket->id}: {$ticket->title}\n";
        $message .= "🏢 Бизнес: " . ($business->name ?? "Не указан") . "\n";
        $message .=
            "👤 Создатель: " . ($creator ? $creator->name : "Не указан") . "\n";
        if ($ticket->description) {
            $message .= "📝 " . substr($ticket->description, 0, 200);
            if (strlen($ticket->description) > 200) {
                $message .= "...";
            }
            $message .= "\n";
        }

        self::sendMessageToUser($admin, $message);
    }

    /**
     * Отправить уведомление о приглашении пользователя в бизнес
     */
    public static function sendBusinessUserInvited(
        \App\Models\BusinessUserInvitation $invitation,
        User $invitedBy,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $business = $invitation->business;
        $role = $invitation->businessRole;

        $message = "👤 Отправлено приглашение\n\n";
        $message .= "🏢 Бизнес: {$business->name}\n";
        $message .= "📧 Email: {$invitation->email}\n";
        $message .= "👔 Роль: {$role->name}\n";
        $message .= "👤 Отправил: {$invitedBy->name}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление о присоединении пользователя к бизнесу
     */
    public static function sendBusinessUserJoined(
        Business $business,
        User $joinedUser,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $message = "✅ Пользователь присоединился\n\n";
        $message .= "🏢 Бизнес: {$business->name}\n";
        $message .= "👤 Пользователь: {$joinedUser->name} ({$joinedUser->email})\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об удалении пользователя из бизнеса
     */
    public static function sendBusinessUserRemoved(
        Business $business,
        User $removedUser,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $message = "❌ Пользователь удалён\n\n";
        $message .= "🏢 Бизнес: {$business->name}\n";
        $message .= "👤 Пользователь: {$removedUser->name} ({$removedUser->email})\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об изменении роли пользователя
     */
    public static function sendBusinessUserRoleChanged(
        Business $business,
        User $user,
        string $oldRole,
        string $newRole,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $oldRoleName =
            \App\Models\BusinessRole::where("slug", $oldRole)->first()?->name ??
            $oldRole;
        $newRoleName =
            \App\Models\BusinessRole::where("slug", $newRole)->first()?->name ??
            $newRole;

        $message = "🔄 Изменена роль пользователя\n\n";
        $message .= "🏢 Бизнес: {$business->name}\n";
        $message .= "👤 Пользователь: {$user->name} ({$user->email})\n";
        $message .= "👔 Было: {$oldRoleName}\n";
        $message .= "👔 Стало: {$newRoleName}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об успешной оплате подписки
     */
    public static function sendSubscriptionPaymentSuccess(
        \App\Models\Invoice $invoice,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $plan = $invoice->plan;

        $message = "✅ Оплата успешна\n\n";
        $message .= "💳 Тариф: {$plan->name}\n";
        $message .= "💰 Сумма: {$invoice->amount} {$invoice->currency}\n";
        $message .= "📅 Дата: {$invoice->paid_at->format("d.m.Y H:i")}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление о неудачной оплате подписки
     */
    public static function sendSubscriptionPaymentFailed(
        \App\Models\Invoice $invoice,
        User $recipient,
        ?string $reason = null,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $plan = $invoice->plan;

        $message = "❌ Оплата не прошла\n\n";
        $message .= "💳 Тариф: {$plan->name}\n";
        $message .= "💰 Сумма: {$invoice->amount} {$invoice->currency}\n";
        if ($reason) {
            $message .= "⚠️ Причина: {$reason}\n";
        }

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об изменении тарифа подписки
     */
    public static function sendSubscriptionPlanChanged(
        \App\Models\Subscription $subscription,
        \App\Models\Plan $oldPlan,
        \App\Models\Plan $newPlan,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $message = "🔄 Тариф изменён\n\n";
        $message .= "📦 Было: {$oldPlan->name}\n";
        $message .= "📦 Стало: {$newPlan->name}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление о продлении подписки
     */
    public static function sendSubscriptionRenewed(
        \App\Models\Subscription $subscription,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $plan = $subscription->plan;
        $endsAt = $subscription->ends_at
            ? $subscription->ends_at->format("d.m.Y")
            : "не указано";

        $message = "🔄 Подписка продлена\n\n";
        $message .= "💳 Тариф: {$plan->name}\n";
        $message .= "📅 Действует до: {$endsAt}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление о начале пробного периода
     */
    public static function sendSubscriptionTrialStarted(
        \App\Models\Subscription $subscription,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $plan = $subscription->plan;
        $trialEndsAt = $subscription->trial_ends_at
            ? $subscription->trial_ends_at->format("d.m.Y")
            : "не указано";

        $message = "🎁 Начат пробный период\n\n";
        $message .= "💳 Тариф: {$plan->name}\n";
        $message .= "📅 Пробный период до: {$trialEndsAt}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление о скором окончании пробного периода
     */
    public static function sendSubscriptionTrialEnding(
        \App\Models\Subscription $subscription,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $plan = $subscription->plan;
        $trialEndsAt = $subscription->trial_ends_at
            ? $subscription->trial_ends_at->format("d.m.Y H:i")
            : "не указано";
        $daysLeft = $subscription->trial_ends_at
            ? now()->diffInDays($subscription->trial_ends_at, false)
            : 0;

        $message = "⏰ Пробный период заканчивается\n\n";
        $message .= "💳 Тариф: {$plan->name}\n";
        $message .= "📅 Заканчивается: {$trialEndsAt}\n";
        $message .= "⏳ Осталось дней: {$daysLeft}\n";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об истечении пробного периода
     */
    public static function sendSubscriptionTrialExpired(
        \App\Models\Subscription $subscription,
        \App\Models\Plan $newPlan,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $oldPlan = $subscription->plan;
        $trialEndedAt = $subscription->trial_ends_at
            ? $subscription->trial_ends_at->format("d.m.Y H:i")
            : "не указано";

        $message = "⏸️ Пробный период истек\n\n";
        $message .= "💳 Старый тариф: {$oldPlan->name}\n";
        $message .= "📅 Истек: {$trialEndedAt}\n";
        $message .= "🔄 Новый тариф: {$newPlan->name}\n\n";
        $message .=
            "Ваш тариф автоматически изменен на бесплатный. Для продолжения использования платных функций оформите подписку.";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об истечении подписки (за 3 дня до окончания)
     */
    public static function sendSubscriptionExpiring(
        \App\Models\Subscription $subscription,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $plan = $subscription->plan;
        $endsAt = $subscription->ends_at
            ? $subscription->ends_at->format("d.m.Y H:i")
            : "не указано";
        $daysLeft = $subscription->ends_at
            ? now()->diffInDays($subscription->ends_at, false)
            : 0;

        $message = "⏰ Подписка истекает\n\n";
        $message .= "💳 Тариф: {$plan->name}\n";
        $message .= "📅 Истекает: {$endsAt}\n";
        $message .= "⏳ Осталось дней: {$daysLeft}\n\n";
        $message .=
            "Продлите подписку для продолжения использования всех функций.";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Отправить уведомление об истечении платной подписки
     */
    public static function sendSubscriptionExpired(
        \App\Models\Subscription $subscription,
        \App\Models\Plan $newPlan,
        User $recipient,
    ): void {
        if (!$recipient->isTelegramConnected()) {
            return;
        }

        $oldPlan = $subscription->plan;
        $expiredAt = $subscription->ends_at
            ? $subscription->ends_at->format("d.m.Y H:i")
            : "не указано";

        $message = "⏸️ Подписка истекла\n\n";
        $message .= "💳 Старый тариф: {$oldPlan->name}\n";
        $message .= "📅 Истекла: {$expiredAt}\n";
        $message .= "🔄 Новый тариф: {$newPlan->name}\n\n";
        $message .=
            "Ваша подписка истекла. Тариф автоматически изменен на бесплатный. Для продолжения использования платных функций оформите новую подписку.";

        self::sendMessageToUser($recipient, $message);
    }

    /**
     * Уведомить о подключении Telegram
     */
    public static function notifyConnected(User $user): void
    {
        if (!$user->isTelegramConnected()) {
            return;
        }

        // Проверяем, включен ли тип уведомления
        if (
            !\App\Services\NotificationSettingsService::isTypeEnabled(
                $user,
                "telegram.connected",
            )
        ) {
            return;
        }

        // In-app уведомление
        \App\Services\NotificationService::send([
            "user_id" => $user->id,
            "type" => "telegram.connected",
            "title" => "Telegram подключен",
            "message" =>
                "Ваш Telegram аккаунт успешно подключен. Теперь вы будете получать уведомления в Telegram.",
            "data" => [],
        ]);

        // Email уведомление (если включено)
        if (
            \App\Services\NotificationSettingsService::shouldSendEmail(
                $user,
                "telegram.connected",
            ) &&
            $user->hasVerifiedEmail()
        ) {
            try {
                $user->notify(new \App\Notifications\Telegram\Connected($user));
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for telegram.connected",
                    [
                        "user_id" => $user->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }

    /**
     * Уведомить об отключении Telegram
     */
    public static function notifyDisconnected(User $user): void
    {
        // Проверяем, включен ли тип уведомления
        if (
            !\App\Services\NotificationSettingsService::isTypeEnabled(
                $user,
                "telegram.disconnected",
            )
        ) {
            return;
        }

        // In-app уведомление
        \App\Services\NotificationService::send([
            "user_id" => $user->id,
            "type" => "telegram.disconnected",
            "title" => "Telegram отключен",
            "message" =>
                "Ваш Telegram аккаунт отключен. Вы больше не будете получать уведомления в Telegram.",
            "data" => [],
        ]);

        // Email уведомление (если включено)
        if (
            \App\Services\NotificationSettingsService::shouldSendEmail(
                $user,
                "telegram.disconnected",
            ) &&
            $user->hasVerifiedEmail()
        ) {
            try {
                $user->notify(
                    new \App\Notifications\Telegram\Disconnected($user),
                );
            } catch (\Exception $e) {
                Log::error(
                    "Failed to send email notification for telegram.disconnected",
                    [
                        "user_id" => $user->id,
                        "error" => $e->getMessage(),
                    ],
                );
            }
        }
    }
}
