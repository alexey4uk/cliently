<?php

namespace App\Notifications\Admin;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket
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
        $ticket = $this->ticket;
        $business = $ticket->business;
        $creator = $ticket->creator();

        return (new MailMessage)
            ->subject('Новый тикет от пользователя #' . $ticket->id)
            ->line('Создан новый тикет от пользователя бизнеса.')
            ->line('Тикет: ' . $ticket->title)
            ->line('Бизнес: ' . ($business->name ?? 'Не указан'))
            ->line('Создатель: ' . ($creator ? $creator->name : 'Не указан'))
            ->line('Описание: ' . ($ticket->description ?? 'Нет описания'))
            ->action('Просмотреть тикет', route('panel.tickets.show', $ticket))
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
            'title' => 'Новый тикет от пользователя',
            'message' => 'Тикет "' . $this->ticket->title . '" создан',
        ];
    }
}
