<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket,
        public ?User $assignedUser = null
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Тикет #' . $this->ticket->id . ' назначен вам')
            ->line('Вам назначен тикет: ' . $this->ticket->title)
            ->line('Описание: ' . $this->ticket->description)
            ->action('Просмотреть тикет', route('panel.tickets.show', $this->ticket));

        if ($this->ticket->priority === 'high' || $this->ticket->priority === 'critical') {
            $message->line('⚠️ Внимание: Тикет имеет высокий приоритет!');
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => 'Вам назначен тикет: ' . $this->ticket->title,
        ];
    }
}
