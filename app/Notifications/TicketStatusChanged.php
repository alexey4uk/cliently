<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket,
        public string $oldStatus,
        public string $newStatus
    ) {}

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
        $statusLabels = [
            'new' => 'Новый',
            'open' => 'В работе',
            'resolved' => 'Решен',
            'closed' => 'Закрыт',
        ];

        return (new MailMessage)
            ->subject('Изменен статус тикета #'.$this->ticket->id)
            ->line('Статус тикета "'.$this->ticket->title.'" изменен')
            ->line('Было: '.($statusLabels[$this->oldStatus] ?? $this->oldStatus))
            ->line('Стало: '.($statusLabels[$this->newStatus] ?? $this->newStatus))
            ->action('Просмотреть тикет', route('panel.tickets.show', $this->ticket))
            ->line('Спасибо за использование нашей системы!');
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Статус тикета изменен: '.$this->ticket->title,
        ];
    }
}
