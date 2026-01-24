<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketSettings;
use App\Models\User;
use App\Models\BusinessRole;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCommentAdded;
use App\Notifications\TicketCreated;
use App\Notifications\TicketStatusChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

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
        if (!$pivotData && $hasPanelPermission) {
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
        $settings = TicketSettings::getForBusiness($ticket->business_id);

        // Загружаем необходимые связи для избежания N+1 запросов
        $ticket->loadMissing('assignedUser');

        // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
        // Уведомляем назначенного пользователя
        // Не проверяем права при создании - права проверяются при отображении уведомления
        if ($ticket->assignedUser) {
            \Illuminate\Support\Facades\Log::info('TicketNotificationService: Sending ticket created notification', [
                'user_id' => $ticket->assignedUser->id,
                'ticket_id' => $ticket->id
            ]);

            NotificationService::send([
                'user_id' => $ticket->assignedUser->id,
                'type' => 'ticket.created',
                'title' => 'Новый тикет #' . $ticket->id,
                'message' => 'Вам назначен тикет: ' . $ticket->title,
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'url' => $this->getTicketRoute($ticket->assignedUser, $ticket)
                ]
            ]);
        }

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        if (! $settings->email_notifications_enabled) {
            return;
        }

        // Уведомляем назначенного пользователя
        if ($ticket->assignedUser) {
            $ticket->assignedUser->notify(new TicketCreated($ticket));
        }

        // Уведомляем получателей из настроек
        if ($settings->email_notification_recipients) {
            foreach ($settings->email_notification_recipients as $email) {
                Notification::route('mail', $email)
                    ->notify(new TicketCreated($ticket));
            }
        }
    }

    /**
     * Отправить уведомление о новом комментарии
     */
    public function notifyCommentAdded(Ticket $ticket, TicketComment $comment): void
    {
        $settings = TicketSettings::getForBusiness($ticket->business_id);

        // Загружаем необходимые связи для избежания N+1 запросов
        $ticket->loadMissing('assignedUser');
        $comment->loadMissing('user');

        // Загружаем бизнес для получения пользователей
        $ticket->loadMissing('business.users');

        $commentAuthorId = $comment->user_id;
        $usersToNotify = [];
        $notifiedUserIds = [$commentAuthorId]; // Исключаем автора комментария

        \Illuminate\Support\Facades\Log::info('TicketNotificationService: notifyCommentAdded started', [
            'ticket_id' => $ticket->id,
            'comment_id' => $comment->id,
            'comment_author_id' => $commentAuthorId,
            'ticket_created_by_type' => $ticket->created_by_type,
            'ticket_created_by_id' => $ticket->created_by_id,
            'ticket_assigned_to' => $ticket->assigned_to,
            'business_id' => $ticket->business_id
        ]);

        // Получаем создателя тикета
        // Создатель тикета ВСЕГДА должен получать уведомление о комментариях (если комментарий не от него)
        // независимо от прав доступа, так как он создал тикет
        $creator = $ticket->creator();
        \Illuminate\Support\Facades\Log::info('TicketNotificationService: creator check', [
            'creator' => $creator ? ['id' => $creator->id, 'name' => $creator->name] : null,
            'comment_author_id' => $commentAuthorId
        ]);

        if ($creator && $creator->id !== $commentAuthorId && !in_array($creator->id, $notifiedUserIds)) {
            $usersToNotify[] = $creator;
            $notifiedUserIds[] = $creator->id;
            \Illuminate\Support\Facades\Log::info('TicketNotificationService: Creator added to notify list (always notified)', ['creator_id' => $creator->id]);
        }

        // Получаем назначенного пользователя (если комментарий не от него)
        if ($ticket->assignedUser && $ticket->assignedUser->id !== $commentAuthorId && !in_array($ticket->assignedUser->id, $notifiedUserIds)) {
            $usersToNotify[] = $ticket->assignedUser;
            $notifiedUserIds[] = $ticket->assignedUser->id;
            \Illuminate\Support\Facades\Log::info('TicketNotificationService: Assigned user added to notify list', ['assigned_user_id' => $ticket->assignedUser->id]);
        }

        // Если тикет не назначен никому, уведомляем всех пользователей бизнеса с правами просмотра тикетов
        // ИЛИ если создатель тикета не получил уведомление (потому что он автор комментария), 
        // но есть другие пользователи с правами - уведомляем их
        if (!$ticket->assigned_to || count($usersToNotify) === 0) {
            $business = $ticket->business;
            if ($business && $business->users) {
                $permissionService = app(\App\Services\BusinessRolePermissionService::class);

                \Illuminate\Support\Facades\Log::info('TicketNotificationService: Checking business users', [
                    'business_id' => $business->id,
                    'users_count' => $business->users->count()
                ]);

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

                    \Illuminate\Support\Facades\Log::info('TicketNotificationService: Checking user permissions', [
                        'user_id' => $user->id,
                        'role_id' => $roleId,
                        'role_slug' => $role->slug ?? null,
                        'has_client_tickets_view' => $hasPermission,
                        'has_panel_tickets_view' => $hasPanelPermission
                    ]);

                    // Уведомляем пользователей с правами просмотра тикетов (бизнес или админ)
                    if ($hasPermission || $hasPanelPermission) {
                        $usersToNotify[] = $user;
                        $notifiedUserIds[] = $user->id;
                        \Illuminate\Support\Facades\Log::info('TicketNotificationService: Business user with tickets permission added', [
                            'user_id' => $user->id,
                            'permission_type' => $hasPermission ? 'client.tickets.view' : 'panel.tickets.view'
                        ]);
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('TicketNotificationService: Business or users not found', [
                    'business' => $business ? 'exists' : 'null',
                    'users' => $business && $business->users ? $business->users->count() : 'null'
                ]);
            }
        }

        \Illuminate\Support\Facades\Log::info('TicketNotificationService: Users to notify', [
            'count' => count($usersToNotify),
            'user_ids' => array_map(fn($u) => $u->id, $usersToNotify)
        ]);

        // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
        // Уведомляем всех пользователей, которым нужно отправить уведомление
        // Не проверяем права при создании - права проверяются при отображении уведомления
        foreach ($usersToNotify as $user) {
            \Illuminate\Support\Facades\Log::info('TicketNotificationService: Sending comment notification', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id
            ]);

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'ticket.comment',
                'title' => 'Новый комментарий к тикету #' . $ticket->id,
                'message' => ($comment->user->name ?? 'Пользователь') . ' добавил(а) комментарий: ' . substr($comment->content, 0, 50) . '...',
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'comment_id' => $comment->id,
                    'url' => $this->getTicketRoute($user, $ticket)
                ]
            ]);
        }

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        if (! $settings->email_notifications_enabled) {
            return;
        }

        // Уведомляем всех пользователей по email
        foreach ($usersToNotify as $user) {
            $user->notify(new TicketCommentAdded($ticket, $comment));
        }

        // Уведомляем получателей из настроек
        if ($settings->email_notification_recipients) {
            foreach ($settings->email_notification_recipients as $email) {
                Notification::route('mail', $email)
                    ->notify(new TicketCommentAdded($ticket, $comment));
            }
        }
    }

    /**
     * Отправить уведомление о назначении тикета
     */
    public function notifyTicketAssigned(Ticket $ticket, ?User $user): void
    {
        if ($user) {
            // === СИСТЕМНОЕ УВЕДОМЛЕНИЕ ===
            \Illuminate\Support\Facades\Log::info('TicketNotificationService: Sending ticket assigned notification', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id
            ]);

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'ticket.assigned',
                'title' => 'Вам назначен тикет #' . $ticket->id,
                'message' => 'Тикет "' . $ticket->title . '" назначен вам',
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'url' => $this->getTicketRoute($user, $ticket)
                ]
            ]);

            // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
            $user->notify(new TicketAssigned($ticket, $user));
        }
    }

    /**
     * Отправить уведомление об изменении статуса
     */
    public function notifyStatusChanged(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        $settings = TicketSettings::getForBusiness($ticket->business_id);

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
            if (!$creator || $ticket->assignedUser->id !== $creator->id) {
                $usersToNotify[] = $ticket->assignedUser;
            }
        }

        $statusText = match ($newStatus) {
            'pending' => 'ожидает',
            'in_progress' => 'в работе',
            'completed' => 'выполнен',
            'cancelled' => 'отменен',
            default => 'обновлен',
        };

        foreach ($usersToNotify as $user) {
            \Illuminate\Support\Facades\Log::info('TicketNotificationService: Sending status changed notification', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            NotificationService::send([
                'user_id' => $user->id,
                'type' => 'ticket.updated',
                'title' => 'Тикет #' . $ticket->id . ' обновлен',
                'message' => 'Статус тикета изменен: ' . $statusText,
                'required_permission' => null, // Не требуем права при создании - проверяем только при отображении
                'data' => [
                    'ticket_id' => $ticket->id,
                    'url' => $this->getTicketRoute($user, $ticket)
                ]
            ]);
        }

        // === ПОЧТОВОЕ УВЕДОМЛЕНИЕ ===
        if (! $settings->email_notifications_enabled) {
            return;
        }

        // Уведомляем назначенного пользователя
        if ($ticket->assignedUser) {
            $ticket->assignedUser->notify(new TicketStatusChanged($ticket, $oldStatus, $newStatus));
        }

        // Уведомляем получателей из настроек
        if ($settings->email_notification_recipients) {
            foreach ($settings->email_notification_recipients as $email) {
                Notification::route('mail', $email)
                    ->notify(new TicketStatusChanged($ticket, $oldStatus, $newStatus));
            }
        }
    }
}
