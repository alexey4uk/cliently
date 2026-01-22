<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketSettings;
use App\Models\User;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketCommentAdded;
use App\Notifications\TicketCreated;
use App\Notifications\TicketStatusChanged;
use Illuminate\Support\Facades\Notification;

class TicketNotificationService
{
    /**
     * Отправить уведомление о создании тикета
     */
    public function notifyTicketCreated(Ticket $ticket): void
    {
        $settings = TicketSettings::getForBusiness($ticket->business_id);

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

        if (! $settings->email_notifications_enabled) {
            return;
        }

        // Уведомляем назначенного пользователя (если комментарий не от него)
        if ($ticket->assignedUser && $ticket->assignedUser->id !== $comment->user_id) {
            $ticket->assignedUser->notify(new TicketCommentAdded($ticket, $comment));
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
            $user->notify(new TicketAssigned($ticket, $user));
        }
    }

    /**
     * Отправить уведомление об изменении статуса
     */
    public function notifyStatusChanged(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        $settings = TicketSettings::getForBusiness($ticket->business_id);

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
