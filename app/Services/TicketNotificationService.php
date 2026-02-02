<?php

namespace App\Services;

use App\Models\BusinessRole;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCommentAdded;
use App\Notifications\TicketCreated;
use App\Notifications\TicketStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TicketNotificationService
{
    /**
     * Получить правильный маршрут для просмотра тикета в зависимости от прав пользователя
     */
    public function getTicketRoute(User $user, Ticket $ticket): string
    {
        $business = $ticket->business;

        // Проверяем бизнес-права пользователя в этом бизнесе
        $pivotData = DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();

        $hasBusinessPermission = false;
        $hasPanelPermission = $user->can('panel.tickets.view');

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
                    // Проверяем бизнес-права на просмотр тикетов
                    $hasBusinessPermission = $permissionService->hasPermission($role->id, 'client.tickets.view');
                }
            }
        }

        // ПРИОРИТЕТ: Если пользователь состоит в бизнесе и имеет бизнес-права - используем клиентский маршрут
        // Это важно, потому что пользователь может иметь и бизнес-права, и админские одновременно
        if ($pivotData && $hasBusinessPermission) {
            return route('tickets.show', $ticket->id);
        }

        // Если пользователь НЕ состоит в бизнесе, но имеет админские права - используем админский маршрут
        if (! $pivotData && $hasPanelPermission) {
            return route('panel.tickets.show', $ticket);
        }

        // По умолчанию - клиентский маршрут (для создателя тикета, даже если нет прав)
        return route('tickets.show', $ticket->id);
    }

    /**
     * Отправить уведомление о создании тикета
     */
    public function notifyTicketCreated(Ticket $ticket): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $ticket->loadMissing('assignedUser');

        // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
        if ($ticket->assignedUser && NotificationSettingsService::isTypeEnabled($ticket->assignedUser, 'ticket.created')) {
            NotificationService::send([
                'user_id' => $ticket->assignedUser->id,
                'type' => 'ticket.created',
                'title' => 'Новый тикет #' . $ticket->id,
                'message' => 'Вам назначен тикет: ' . $ticket->title,
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'url' => $this->getTicketRoute($ticket->assignedUser, $ticket),
                ],
            ]);
        }

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        if ($ticket->assignedUser && NotificationSettingsService::isTypeEnabled($ticket->assignedUser, 'ticket.created') && config('tickets.notifications.email_enabled')) {
            if (NotificationSettingsService::shouldSendEmail($ticket->assignedUser, 'ticket.created')) {
                try {
                    $ticket->assignedUser->notify(new TicketCreated($ticket));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send email notification for ticket.created', [
                        'user_id' => $ticket->assignedUser->id,
                        'ticket_id' => $ticket->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Уведомляем получателей из настроек (без проверки настроек пользователя, т.к. это внешние email)
        if (config('tickets.notifications.recipients', []) && config('tickets.notifications.email_enabled')) {
            foreach (config('tickets.notifications.recipients', []) as $email) {
                try {
                    Notification::route('mail', $email)
                        ->notify(new TicketCreated($ticket));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send email notification to recipient', [
                        'email' => $email,
                        'ticket_id' => $ticket->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // === TELEGRAM УВЕДОМЛЕНИЕ ===
        if ($ticket->assignedUser && NotificationSettingsService::isTypeEnabled($ticket->assignedUser, 'ticket.created') && NotificationSettingsService::shouldSendTelegram($ticket->assignedUser, 'ticket.created')) {
            try {
                TelegramNotificationService::sendTicketCreated($ticket, $ticket->assignedUser);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send telegram notification for ticket.created', [
                    'user_id' => $ticket->assignedUser->id,
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Отправить уведомление о новом комментарии
     */
    public function notifyCommentAdded(Ticket $ticket, TicketComment $comment): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $ticket->loadMissing('assignedUser');
        $comment->loadMissing('user');

        // Загружаем бизнес для получения пользователей
        $ticket->loadMissing('business.users');

        $commentAuthorId = $comment->user_id;
        $usersToNotify = [];
        $notifiedUserIds = [$commentAuthorId]; // Исключаем автора комментария

        // Получаем создателя тикета
        // Создатель тикета ВСЕГДА должен получать уведомление о комментариях (если комментарий не от него)
        // независимо от прав доступа, так как он создал тикет
        $creator = $ticket->creator();

        if ($creator && $creator->id !== $commentAuthorId && ! in_array($creator->id, $notifiedUserIds)) {
            $usersToNotify[] = $creator;
            $notifiedUserIds[] = $creator->id;
        }

        // Получаем назначенного пользователя (если комментарий не от него)
        if ($ticket->assignedUser && $ticket->assignedUser->id !== $commentAuthorId && ! in_array($ticket->assignedUser->id, $notifiedUserIds)) {
            $usersToNotify[] = $ticket->assignedUser;
            $notifiedUserIds[] = $ticket->assignedUser->id;
        }

        // Если тикет не назначен никому, уведомляем всех пользователей бизнеса с правами просмотра тикетов
        // ИЛИ если создатель тикета не получил уведомление (потому что он автор комментария),
        // но есть другие пользователи с правами - уведомляем их
        if (! $ticket->assigned_to || count($usersToNotify) === 0) {
            $business = $ticket->business;
            if ($business && $business->users) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);

                foreach ($business->users as $user) {
                    // Пропускаем автора комментария и уже добавленных пользователей
                    if (in_array($user->id, $notifiedUserIds)) {
                        continue;
                    }

                    // Получаем роль пользователя в бизнесе через pivot
                    $pivotData = DB::table('business_user')
                        ->where('user_id', $user->id)
                        ->where('business_id', $business->id)
                        ->first();

                    $hasPermission = false;
                    $roleId = null;

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
                            $roleId = $role->id;
                            // Owner всегда имеет все права
                            if ($role->slug === 'owner') {
                                $hasPermission = true;
                            } else {
                                // Проверяем бизнес-права на просмотр тикетов
                                $hasPermission = $permissionService->hasPermission($roleId, 'client.tickets.view');
                            }
                        }
                    }

                    // Также проверяем административные права (panel.tickets.view) для админов
                    $hasPanelPermission = $user->can('panel.tickets.view');

                    // Уведомляем пользователей с правами просмотра тикетов (бизнес или админ)
                    if ($hasPermission || $hasPanelPermission) {
                        $usersToNotify[] = $user;
                        $notifiedUserIds[] = $user->id;
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('TicketNotificationService: Business or users not found', [
                    'business' => $business ? 'exists' : 'null',
                    'users' => $business && $business->users ? $business->users->count() : 'null',
                ]);
            }
        }

        // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
        foreach ($usersToNotify as $user) {
            if (! NotificationSettingsService::isTypeEnabled($user, 'ticket.comment')) {
                continue;
            }
            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'ticket.comment',
                'title' => 'Новый комментарий к тикету #' . $ticket->id,
                'message' => ($comment->user->name ?? 'Пользователь') . ' добавил(а) комментарий: ' . substr($comment->content, 0, 50) . '...',
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'comment_id' => $comment->id,
                    'url' => $this->getTicketRoute($user, $ticket),
                ],
            ]);
        }

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        if (config('tickets.notifications.email_enabled')) {
            foreach ($usersToNotify as $user) {
                if (! NotificationSettingsService::isTypeEnabled($user, 'ticket.comment')) {
                    continue;
                }
                if (NotificationSettingsService::shouldSendEmail($user, 'ticket.comment')) {
                    try {
                        $user->notify(new TicketCommentAdded($ticket, $comment));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send email notification for ticket.comment', [
                            'user_id' => $user->id,
                            'ticket_id' => $ticket->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Уведомляем получателей из настроек (без проверки настроек пользователя, т.к. это внешние email)
            if (config('tickets.notifications.recipients', [])) {
                foreach (config('tickets.notifications.recipients', []) as $email) {
                    try {
                        Notification::route('mail', $email)
                            ->notify(new TicketCommentAdded($ticket, $comment));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send email notification to recipient', [
                            'email' => $email,
                            'ticket_id' => $ticket->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        // === TELEGRAM УВЕДОМЛЕНИЕ ===
        foreach ($usersToNotify as $user) {
            if (! NotificationSettingsService::isTypeEnabled($user, 'ticket.comment')) {
                continue;
            }
            if (NotificationSettingsService::shouldSendTelegram($user, 'ticket.comment')) {
                try {
                    TelegramNotificationService::sendTicketCommentAdded($ticket, $comment, $user);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send telegram notification for ticket.comment', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Отправить уведомление о назначении тикета
     */
    public function notifyTicketAssigned(Ticket $ticket, ?User $user): void
    {
        if (! $user) {
            return;
        }

        // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
        // Отправляем всегда, даже если тип уведомления отключен - назначение тикета критично
        NotificationService::send([
            'user_id' => $user->id,
            'type' => 'ticket.assigned',
            'title' => 'Вам назначен тикет #' . $ticket->id,
            'message' => 'Тикет "' . $ticket->title . '" назначен вам',
            'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
            'data' => [
                'ticket_id' => $ticket->id,
                'url' => $this->getTicketRoute($user, $ticket),
            ],
        ]);

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        // Отправляем только если тип уведомления включен
        if (NotificationSettingsService::isTypeEnabled($user, 'ticket.assigned') && NotificationSettingsService::shouldSendEmail($user, 'ticket.assigned')) {
            try {
                $user->notify(new TicketAssigned($ticket, $user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send email notification for ticket.assigned', [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // === TELEGRAM УВЕДОМЛЕНИЕ ===
        // Отправляем только если тип уведомления включен
        if (NotificationSettingsService::isTypeEnabled($user, 'ticket.assigned') && NotificationSettingsService::shouldSendTelegram($user, 'ticket.assigned')) {
            try {
                TelegramNotificationService::sendTicketAssigned($ticket, $user);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send telegram notification for ticket.assigned', [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Отправить уведомление об изменении статуса
     */
    public function notifyStatusChanged(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        // Загружаем необходимые связи для избежания N+1 запросов
        $ticket->loadMissing('assignedUser');

        // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
        // Уведомляем назначенного пользователя и создателя тикета
        $usersToNotify = [];

        // Получаем создателя тикета
        $creator = $ticket->creator();
        if ($creator) {
            $usersToNotify[] = $creator;
        }

        // Получаем назначенного пользователя (если он не создатель)
        if ($ticket->assignedUser) {
            if (! $creator || $ticket->assignedUser->id !== $creator->id) {
                $usersToNotify[] = $ticket->assignedUser;
            }
        }

        $statusText = match ($newStatus) {
            'pending' => 'ожидает',
            'open' => 'в работе',
            'completed' => 'выполнен',
            'cancelled' => 'отменен',
            default => 'обновлен',
        };

        foreach ($usersToNotify as $user) {
            if (! NotificationSettingsService::isTypeEnabled($user, 'ticket.status_changed')) {
                continue;
            }

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'ticket.status_changed',
                'title' => 'Тикет #' . $ticket->id . ' обновлен',
                'message' => 'Статус тикета изменен: ' . $statusText,
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'url' => $this->getTicketRoute($user, $ticket),
                ],
            ]);
        }

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        if (config('tickets.notifications.email_enabled')) {
            foreach ($usersToNotify as $user) {
                if (! NotificationSettingsService::isTypeEnabled($user, 'ticket.status_changed')) {
                    continue;
                }
                if (NotificationSettingsService::shouldSendEmail($user, 'ticket.status_changed')) {
                    try {
                        $user->notify(new TicketStatusChanged($ticket, $oldStatus, $newStatus));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send email notification for ticket.status_changed', [
                            'user_id' => $user->id,
                            'ticket_id' => $ticket->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Уведомляем получателей из настроек (без проверки настроек пользователя, т.к. это внешние email)
            if (config('tickets.notifications.recipients', [])) {
                foreach (config('tickets.notifications.recipients', []) as $email) {
                    try {
                        Notification::route('mail', $email)
                            ->notify(new TicketStatusChanged($ticket, $oldStatus, $newStatus));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send email notification to recipient', [
                            'email' => $email,
                            'ticket_id' => $ticket->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        // === TELEGRAM УВЕДОМЛЕНИЕ ===
        foreach ($usersToNotify as $user) {
            if (! NotificationSettingsService::isTypeEnabled($user, 'ticket.status_changed')) {
                continue;
            }
            if (NotificationSettingsService::shouldSendTelegram($user, 'ticket.status_changed')) {
                try {
                    TelegramNotificationService::sendTicketStatusChanged($ticket, $user, $oldStatus, $newStatus);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send telegram notification for ticket.status_changed', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
